"""Normaliza salida RAW de Gemini (JSONL con posible fence markdown) al esquema común.

Uso:
  python normalize.py --raw 01_RAW/gemini_family_0_300.txt \
      --out 02_NORMALIZED/observations.jsonl --provider gemini --model gemini-2.5-flash
"""
import argparse
import csv
import json
from pathlib import Path

SCHEMA = ["family", "candidate", "brand", "start_s", "end_s", "x", "y",
          "l1_conf", "l2_conf", "provider", "model", "evidence",
          "source_ref", "clearance_relevant", "needs_family_review"]


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--raw", required=True)
    ap.add_argument("--out", required=True)
    ap.add_argument("--provider", default="gemini")
    ap.add_argument("--model", default="")
    ap.add_argument("--source-ref", default="")
    args = ap.parse_args()

    obs, skipped = [], 0
    for line in Path(args.raw).read_text(encoding="utf-8").splitlines():
        line = line.strip().removeprefix("```json").removeprefix("```").strip()
        if not line or line == "```":
            continue
        try:
            o = json.loads(line)
        except json.JSONDecodeError:
            skipped += 1
            continue
        fam = o.get("family", "")
        obs.append({
            "family": fam,
            "candidate": o.get("candidate"),
            "brand": "UNKNOWN",
            "start_s": float(o.get("start_s", 0)),
            "end_s": float(o.get("end_s", 0)),
            "x": None, "y": None,  # Prompt 1 no da cajas; solo segmentos
            "l1_conf": float(o.get("l1_conf", 0)),
            "l2_conf": None,
            "provider": args.provider,
            "model": args.model,
            "evidence": o.get("evidence", ""),
            "source_ref": args.source_ref,
            "clearance_relevant": 0,
            "needs_family_review": 1 if fam == "NEEDS_FAMILY_REVIEW" else 0,
        })

    obs.sort(key=lambda o: (o["start_s"], o["end_s"]))
    out = Path(args.out)
    out.parent.mkdir(parents=True, exist_ok=True)
    with out.open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=SCHEMA)
        w.writeheader()
        w.writerows(obs)
    print(f"OK: {len(obs)} observaciones en {out} (descartadas: {skipped})")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
