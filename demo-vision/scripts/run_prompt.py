"""Ejecuta un prompt multimodal (vídeo en GCS) contra Gemini en Vertex AI.

Uso:
  pip install google-genai
  gcloud auth application-default login   # con alex.mohamed@i-want-it.es
  python run_prompt.py --prompt prompts/01_family_inventory.md \
      --video gs://iwantit-exp-emily-s03e02/video/clip_0_300.mp4 \
      --out 01_RAW/gemini_family_0_300.txt
"""
import argparse
import sys
from pathlib import Path

PROJECT = "iwantit-max-analysis"
LOCATION = "europe-west1"
MODEL = "gemini-2.5-flash"  # si da 404, probar con el modelo estable vigente


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--prompt", required=True, help="Fichero .md con el prompt")
    ap.add_argument("--video", required=True, help="URI GCS del vídeo")
    ap.add_argument("--out", required=True, help="Fichero de salida (texto RAW)")
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
    video_part = types.Part.from_uri(file_uri=args.video, mime_type="video/mp4")

    print(f"Llamando a {args.model} con {args.video} ...", flush=True)
    response = client.models.generate_content(
        model=args.model,
        contents=[video_part, prompt],
        config=types.GenerateContentConfig(
            temperature=0.2,
            max_output_tokens=65536,
        ),
    )
    out = Path(args.out)
    out.parent.mkdir(parents=True, exist_ok=True)
    out.write_text(response.text or "", encoding="utf-8")
    print(f"OK: {len(response.text or '')} caracteres en {out}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
