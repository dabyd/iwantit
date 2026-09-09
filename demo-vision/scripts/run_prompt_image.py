"""Prompt 2 sobre imágenes: una observación + 3 frames -> marca o UNKNOWN.

Uso:
  pip install google-genai
  gcloud auth application-default login
  python run_prompt_image.py --prompt prompts/02_brand_enrichment.md \
      --obs '{"family":"lata","start_s":157.7,"end_s":162.5}' \
      --images crops/lata_coca_158-163_t158.jpg crops/lata_coca_158-163_t160.jpg crops/lata_coca_158-163_t162.jpg \
      --out 01_RAW/brand_lata_coca.txt

Bucle para los 10 MISS (bash): ver docs/INFORME_MAXIMUM.md §6.
"""
import argparse
import sys
from pathlib import Path

PROJECT = "iwantit-max-analysis"
LOCATION = "europe-west1"
MODEL = "gemini-2.5-flash"


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--prompt", required=True)
    ap.add_argument("--obs", required=True, help="JSON de la observación L1")
    ap.add_argument("--images", nargs="+", required=True)
    ap.add_argument("--out", required=True)
    ap.add_argument("--model", default=MODEL)
    args = ap.parse_args()

    try:
        from google import genai
        from google.genai import types
    except ImportError:
        print("Falta SDK: pip install google-genai", file=sys.stderr)
        return 1

    prompt = Path(args.prompt).read_text(encoding="utf-8")
    client = genai.Client(vertexai=True, project=PROJECT, location=LOCATION)
    parts = [prompt, f"Observación a enriquecer: {args.obs}"]
    for img in args.images:
        parts.append(types.Part.from_bytes(
            data=Path(img).read_bytes(), mime_type="image/jpeg"))

    print(f"Llamando a {args.model} con {len(args.images)} imágenes ...", flush=True)
    response = client.models.generate_content(
        model=args.model, contents=parts,
        config=types.GenerateContentConfig(temperature=0.1, max_output_tokens=2048))
    out = Path(args.out)
    out.parent.mkdir(parents=True, exist_ok=True)
    out.write_text(response.text or "", encoding="utf-8")
    print(f"OK en {out}:\n{(response.text or '').strip()[:600]}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
