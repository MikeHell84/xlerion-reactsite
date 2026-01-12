#!/usr/bin/env python3
"""
Generate PNG placeholder images for each SVG wireframe.
This is a fallback when native SVG rasterizers aren't available.
"""
from PIL import Image, ImageDraw, ImageFont
from pathlib import Path
import sys

# Corporate colors (can be adjusted)
PRIMARY = (10, 255, 233)  # #0affe9 (from earlier user spec)
BG = (255, 255, 255)
PANEL = (244, 246, 248)
CARD = (255, 255, 255)
TEXT = (20, 20, 20)

BASE = Path(__file__).resolve().parents[1] / 'docs' / 'wireframes'
PNG_DIR = BASE / 'png'
PNG_DIR.mkdir(parents=True, exist_ok=True)

LOGO_CANDIDATES = [
    Path(__file__).resolve().parents[1] / 'media' / 'Diseño-de-logotipo-X.png',
    Path(__file__).resolve().parents[1] / 'media' / 'Diseño de logotipo X.png',
    Path(__file__).resolve().parents[1] / 'public' / 'media' / 'Diseño-de-logotipo-X.png',
]

def find_logo():
    for p in LOGO_CANDIDATES:
        if p.exists():
            return p
    return None

svgs = list(BASE.glob('*.svg'))
if not svgs:
    print('No SVG files found in', BASE)
    sys.exit(1)

logo_path = find_logo()
if logo_path:
    try:
        logo_img = Image.open(logo_path).convert('RGBA')
        # scale logo reasonably later
    except Exception:
        logo_img = None
else:
    logo_img = None

# export sizes: name -> (width, height)
SIZES = {
    'large': (1400, 900),
    'presentation': (1200, 800),
    'thumb': (600, 400),
    'x2': (2800, 1800),
}

for svg in svgs:
    name = svg.stem
    for label, (W, H) in SIZES.items():
        out = PNG_DIR / f"{name}_{label}.png"
        print('Creating', out)
        img = Image.new('RGBA', (W, H), color=BG)
        d = ImageDraw.Draw(img)
        # fonts
        try:
            font = ImageFont.truetype('arial.ttf', max(12, int(W/60)))
            title_font = ImageFont.truetype('arialbd.ttf', max(18, int(W/40)))
        except Exception:
            font = ImageFont.load_default()
            title_font = ImageFont.load_default()

        # header bar with primary color
        header_h = int(H * 0.08)
        d.rectangle([0, 0, W, header_h], fill=PRIMARY)
        d.text((int(W*0.02), int(header_h*0.25)), f'Wireframe: {name.replace("_"," ")}', font=title_font, fill=(0,0,0))

        # logo if available
        if logo_img:
            try:
                # scale logo to header height * 0.75
                l_h = int(header_h * 0.75)
                ratio = l_h / logo_img.height
                l_w = int(logo_img.width * ratio)
                logo_resized = logo_img.resize((l_w, l_h), Image.LANCZOS)
                img.paste(logo_resized, (W - l_w - int(W*0.02), int(header_h*0.125)), logo_resized)
            except Exception:
                pass

        # draw three sample cards/columns
        cols = 3 if W > 800 else 1
        margin_x = int(W * 0.025)
        col_w = int((W - margin_x*2 - (cols-1)*margin_x) / cols)
        y0 = header_h + int(H * 0.04)
        card_h = int(H * 0.55)
        for i in range(cols):
            x = margin_x + i * (col_w + margin_x)
            d.rectangle([x, y0, x + col_w, y0 + card_h], fill=PANEL, outline=(220,225,230))
            # sample card text
            d.text((x + int(col_w*0.03), y0 + int(card_h*0.03)), 'Card / Column', font=font, fill=TEXT)

        # footer note
        note = 'Placeholder PNG — replace with full export for final assets.'
        d.text((margin_x, H - int(H*0.04)), note, font=font, fill=(100,110,120))

        # save
        img.convert('RGB').save(out, 'PNG')

print('Enhanced placeholders created in', PNG_DIR)
