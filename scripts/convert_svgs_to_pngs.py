#!/usr/bin/env python3
"""
Convert all SVG files in docs/wireframes/ to PNGs in docs/wireframes/png/ using CairoSVG.
"""
import os
from pathlib import Path

try:
    import cairosvg
except Exception as e:
    raise

SRC_DIR = Path(__file__).resolve().parents[1] / 'docs' / 'wireframes'
DST_DIR = SRC_DIR / 'png'
DST_DIR.mkdir(parents=True, exist_ok=True)

svgs = list(SRC_DIR.glob('*.svg'))
if not svgs:
    print('No SVG files found in', SRC_DIR)
    raise SystemExit(1)

for svg in svgs:
    out = DST_DIR / (svg.stem + '.png')
    print(f'Converting {svg.name} -> {out.name}')
    try:
        cairosvg.svg2png(url=str(svg), write_to=str(out))
    except Exception as e:
        print('Failed to convert', svg, e)
        raise

print('Conversion complete. PNGs in', DST_DIR)
