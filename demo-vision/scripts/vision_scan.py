"""Barrido Vision 1 FPS: LOGO + TEXT + OBJECT_LOCALIZATION por frame.

Guarda RAW por frame (01_RAW/vision/frame_NNNN.json) y un agregado
normalizado (02_NORMALIZED/vision.jsonl) con cajas 0-1 para la
evaluación espacial ±10px.

Uso:
  pip install google-cloud-vision
  gcloud auth application-default login   # alex.mohamed@i-want-it.es
  python vision_scan.py --frames demo-vision/frames \
      --raw demo-vision/01_RAW/vision --out demo-vision/02_NORMALIZED/vision.jsonl
"""
import argparse
import base64
import csv
import json
from pathlib import Path

FIELDS = ["family", "label", "brand", "frame", "t0", "t1", "x", "y", "w", "h",
          "l1_conf", "provider", "model", "evidence", "source_ref"]


def norm_poly(vertices, size):
    # vertices con x/y normalizados (0-1) si la API los da así; si no, dividir
    W, H = size
    xs, ys = [], []
    for v in vertices:
        x, y = v.get("x", 0), v.get("y", 0)
        xs.append(x / W if x > 1 else x)
        ys.append(y / H if y > 1 else y)
    if not xs:
        return None
    x0, x1, y0, y1 = min(xs), max(xs), min(ys), max(ys)
    return (round(x0, 4), round(y0, 4), round(x1 - x0, 4), round(y1 - y0, 4))


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--frames", required=True)
    ap.add_argument("--raw", required=True)
    ap.add_argument("--out", required=True)
    ap.add_argument("--limit", type=int, default=0, help="0 = todos")
    args = ap.parse_args()

    from google.cloud import vision
    from PIL import Image as PILImage

    client = vision.ImageAnnotatorClient()
    feats = [
        {"type_": vision.Feature.Type.LOGO_DETECTION, "max_results": 20},
        {"type_": vision.Feature.Type.TEXT_DETECTION, "max_results": 20},
        {"type_": vision.Feature.Type.OBJECT_LOCALIZATION, "max_results": 30},
    ]
    rawdir = Path(args.raw)
    rawdir.mkdir(parents=True, exist_ok=True)
    frames = sorted(Path(args.frames).glob("frame_*.jpg"))
    if args.limit:
        frames = frames[:args.limit]
    print(f"Frames a procesar: {len(frames)}")

    rows = []
    for i, fp in enumerate(frames, 1):
        n = int(fp.stem.split("_")[1])
        with PILImage.open(fp) as im:
            W, H = im.size
        content = fp.read_bytes()
        req = {"image": {"content": base64.b64encode(content).decode()},
               "features": feats}
        resp = client.annotate_image(request=req)
        (rawdir / f"frame_{n:04d}.json").write_text(
            json.dumps({
                "logos": [{"description": a.description, "score": a.score,
                           "box": [(v.x, v.y) for v in a.bounding_poly.vertices]}
                          for a in resp.logo_annotations],
                "texts": [{"description": t.description[:200], "locale": t.locale}
                          for t in resp.text_annotations[:5]],
                "objects": [{"name": o.name, "score": o.score,
                             "box": [{"x": v.x, "y": v.y}
                                     for v in o.bounding_poly.normalized_vertices]}
                            for o in resp.localized_object_annotations],
            }, ensure_ascii=False)[:200000], encoding="utf-8")

        t0, t1 = float(n - 1), float(n)
        for a in resp.logo_annotations:
            b = norm_poly([{"x": v.x, "y": v.y} for v in a.bounding_poly.vertices], (W, H))
            rows.append([None, f"logo:{a.description}", a.description, n, t0, t1,
                         *(b or (None,) * 4), round(a.score, 3),
                         "vision", "logo_detection", f"logo {a.description}", fp.name])
        for o in resp.localized_object_annotations:
            b = norm_poly([{"x": v.x, "y": v.y}
                           for v in o.bounding_poly.normalized_vertices], (1, 1))
            rows.append([None, f"obj:{o.name}", "UNKNOWN", n, t0, t1,
                         *(b or (None,) * 4), round(o.score, 3),
                         "vision", "object_localization", f"objeto {o.name}", fp.name])
        if i % 50 == 0:
            print(f"  {i}/{len(frames)} ...", flush=True)

    out = Path(args.out)
    out.parent.mkdir(parents=True, exist_ok=True)
    with out.open("w", encoding="utf-8", newline="") as f:
        w = csv.writer(f)
        w.writerow(FIELDS)
        w.writerows(rows)
    print(f"OK: {len(rows)} detecciones en {out}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
