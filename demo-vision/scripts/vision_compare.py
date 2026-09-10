"""Cruza el agregado Vision contra los 14 MISS del Prompt 1.

Uso:
  python vision_compare.py --vision 02_NORMALIZED/vision.jsonl
"""
import argparse
import csv

# (familia_GT, producto, marca, t_start, t_end, keywords a buscar en label/logo/texto)
MISS = [
    ("plaza", "Place de l'estrapade", "Locations", 12.0, 65.0, ["square", "plaza", "street"]),
    ("restaurante_bar", "Restaurant Terra Nera", "Restaurants", 12.0, 69.0, ["restaurant"]),
    ("abrigo", "Capotto in yellow", "Dolce & Gabbana", 107.8, 298.4, ["coat", "jacket"]),
    ("taza", "[AI] Cup", "AI Generated", 110.7, 274.6, ["cup", "coffee cup", "mug"]),
    ("jarra", "Daily8 Water Jug", "Bluewave", 148.0, 297.7, ["jug", "bottle", "pitcher"]),
    ("lata", "Coca-cola", "Coca-Cola", 157.7, 162.5, ["coca", "cola", "can", "soda"]),
    ("movil", "Galaxy S23 Ultra Rosa", "Samsung", 165.6, 207.4, ["phone", "mobile", "samsung", "galaxy"]),
    ("silla", "[AI] Chair", "AI Generated", 167.3, 298.5, ["chair"]),
    ("chaqueta", "Velvet fuchsia jacket", "Elie Saab", 167.9, 207.8, ["jacket", "suit"]),
    ("portatil", "MacBook Air", "Apple", 180.0, 234.4, ["laptop", "apple", "macbook", "computer"]),
    ("reloj", "Datejust", "Rolex", 229.3, 258.2, ["watch", "rolex", "clock"]),
    ("gafas", "[AI] Glasses", "AI Generated", 261.9, 270.0, ["glasses", "sunglasses", "spectacles"]),
    ("escaparate", "Tiffany Champs Elisses", "Locations", 279.8, 281.8, ["tiffany", "store", "shop"]),
    ("artwork", "[AI] Picture/Frame", "AI Generated", 281.7, 286.0, ["picture", "painting", "frame"]),
]


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--vision", required=True)
    args = ap.parse_args()
    det = list(csv.DictReader(open(args.vision, encoding="utf-8")))
    print(f"Detecciones Vision: {len(det)}\n== Caza de los 14 MISS ==")
    hit_n = 0
    for fam, prod, marca, t0, t1, kws in MISS:
        hits = [d for d in det
                if t0 - 1 <= float(d["t0"]) <= t1 + 1
                and any(k in (d["label"] or "").lower() for k in kws)]
        tag = "RESCATADO" if hits else "sigue MISS"
        hit_n += 1 if hits else 0
        print(f"  [{tag}] {prod} t[{t0:.0f}->{t1:.0f}] kw={kws[0]}: {len(hits)} detecciones")
        for h in hits[:3]:
            print(f"      f{int(float(h['frame'])):04d} {h['label']} conf={h['l1_conf']} "
                  f"box=({h['x']},{h['y']},{h['w']},{h['h']})")
    print(f"\nRescatados por Vision: {hit_n}/14")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
