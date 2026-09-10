"""Compara observaciones AI normalizadas vs ground truth manual (0-300s).

Tolerancia temporal: +-TOL_S (defecto 1.0s). Sin cajas en AI -> sin
evaluación espacial en este paso (pendiente de Vision/crops).

Uso:
  python compare.py --gt data/ground_truth_0_300_familias.csv \
      --ai 02_NORMALIZED/observations.jsonl --tol 1.0
"""
import argparse
import csv
import json
from collections import defaultdict

# Familias AI fuera del alcance del GT de producto (no son falsos positivos)
OUT_OF_SCOPE = {"persona", "cara", "grupo_personas", "texto_visible"}


def overlap(a0, a1, b0, b1, tol):
    return a0 <= b1 + tol and b0 <= a1 + tol


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--gt", required=True)
    ap.add_argument("--ai", required=True)
    ap.add_argument("--tol", type=float, default=1.0)
    args = ap.parse_args()

    gt = [r for r in csv.DictReader(open(args.gt, encoding="utf-8"))
          if r["family_L1"] != "NEEDS_FAMILY_REVIEW"]
    gt_review = [r for r in csv.DictReader(open(args.gt, encoding="utf-8"))
                 if r["family_L1"] == "NEEDS_FAMILY_REVIEW"]
    ai = []
    with open(args.ai, encoding="utf-8") as f:
        rd = csv.DictReader(f)
        if "family" in (rd.fieldnames or []):  # observations.csv del normalize.py
            for r in rd:
                r["start_s"] = float(r["start_s"] or 0)
                r["end_s"] = float(r["end_s"] or 0)
                r["l1_conf"] = float(r["l1_conf"] or 0)
                ai.append(r)
        else:  # JSONL puro
            ai = [json.loads(l) for l in open(args.ai, encoding="utf-8") if l.strip()]
    ai = [o for o in ai if o.get("family") and o["family"] != "NEEDS_FAMILY_REVIEW"]

    print(f"GT evaluable: {len(gt)} productos | GT en revisión: {len(gt_review)} | "
          f"AI obs: {len(ai)} (tol=±{args.tol}s)\n")

    # 1. Recall por producto GT
    per_fam = defaultdict(lambda: {"n": 0, "match": 0})
    misses = []
    for g in gt:
        fam, t0, t1 = g["family_L1"], float(g["t_start"]), float(g["t_end"])
        hit = any(o["family"] == fam and overlap(t0, t1, o["start_s"], o["end_s"], args.tol)
                  for o in ai)
        per_fam[fam]["n"] += 1
        per_fam[fam]["match"] += 1 if hit else 0
        if not hit:
            misses.append(f"  MISS [{fam}] {g['producto']} ({g['marca']}) t[{t0:.1f}->{t1:.1f}]")

    print("== RECALL L1 por familia (producto GT con solape AI) ==")
    tot_n = tot_m = 0
    for fam in sorted(per_fam):
        n, m = per_fam[fam]["n"], per_fam[fam]["match"]
        tot_n += n
        tot_m += m
        print(f"  {fam:20s} {m}/{n}")
    print(f"  {'TOTAL':20s} {tot_m}/{tot_n} = {100*tot_m/max(tot_n,1):.1f}%\n")

    if misses:
        print("== AI MISS ==")
        print("\n".join(misses) + "\n")

    # 2. Extras AI sin contrapartida GT (posibles descubrimientos)
    print("== AI EXTRAS (familia AI sin solape GT mismo tiempo+familia) ==")
    gt_segs = [(g["family_L1"], float(g["t_start"]), float(g["t_end"])) for g in gt]
    shown = 0
    for o in sorted(ai, key=lambda x: x["start_s"]):
        fam = o["family"]
        tag = "OUT_OF_SCOPE" if fam in OUT_OF_SCOPE else "DISCOVERY?"
        if fam in OUT_OF_SCOPE:
            continue  # alcance producto: persona/cara/texto no puntúan
        if not any(f == fam and overlap(o["start_s"], o["end_s"], t0, t1, args.tol)
                   for f, t0, t1 in gt_segs):
            print(f"  {tag} [{fam}] t[{o['start_s']:.1f}->{o['end_s']:.1f}] "
                  f"conf={o['l1_conf']:.1f} :: {o['evidence'][:80]}")
            shown += 1
    if not shown:
        print("  (ninguno)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
