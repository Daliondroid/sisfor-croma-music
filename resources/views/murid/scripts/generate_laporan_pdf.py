#!/usr/bin/env python3
"""
generate_laporan_pdf.py
Dipanggil oleh Laravel: python3 generate_laporan_pdf.py '<json>' output.pdf
JSON keys: nama_murid, nama_guru, nama_program, bulan_label, kota, skor,
           pct_hadir, total_hadir, total_sesi, evaluasi_bulanan,
           jadwals: [{no, tanggal, materi}]
"""

import sys, json
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import mm
from reportlab.pdfgen import canvas
from reportlab.platypus import Table, TableStyle, Paragraph
from reportlab.lib.styles import ParagraphStyle

def skor_color(skor):
    mapa = {
        'A+': '#15803d', 'A': '#16a34a', 'A-': '#059669',
        'B+': '#1d4ed8', 'B': '#2563eb', 'B-': '#4f46e5',
        'C+': '#a16207', 'C': '#b45309', 'C-': '#c2410c',
    }
    return mapa.get(str(skor), '#6b7280')

def run(data: dict, out_path: str):
    W, H = A4
    mx = 20 * mm
    c = canvas.Canvas(out_path, pagesize=A4)

    y = H - 18*mm

    # ── Header ────────────────────────────────────────────────
    c.setFillColor(colors.HexColor("#003d80"))
    c.rect(0, H - 28*mm, W, 28*mm, fill=1, stroke=0)
    c.setFillColor(colors.white)
    c.setFont("Helvetica-Bold", 15)
    c.drawCentredString(W/2, H - 14*mm, "Capaian Belajar Murid")
    c.setFont("Helvetica", 9)
    c.drawCentredString(W/2, H - 21*mm, "CROMA MUSIC — Sistem Manajemen Sekolah Musik")

    y = H - 36*mm

    # ── Info Murid ────────────────────────────────────────────
    info = [
        ("Nama",           data.get("nama_murid", "-")),
        ("Program Kursus", data.get("nama_program", "-")),
        ("Guru",           data.get("nama_guru", "-")),
        ("Bulan",          data.get("bulan_label", "-")),
    ]
    label_x = mx
    colon_x = mx + 40*mm
    value_x = colon_x + 5*mm

    for label, value in info:
        c.setFillColor(colors.HexColor("#111827"))
        c.setFont("Helvetica-Bold", 10.5)
        c.drawString(label_x, y, label)
        c.setFont("Helvetica", 10.5)
        c.drawString(colon_x, y, ":")
        c.drawString(value_x,  y, str(value))
        y -= 7*mm

    y -= 3*mm
    c.setStrokeColor(colors.HexColor("#e5e7eb"))
    c.setLineWidth(0.8)
    c.line(mx, y, W - mx, y)
    y -= 8*mm

    # ── Tabel Pertemuan ───────────────────────────────────────
    c.setFillColor(colors.HexColor("#111827"))
    c.setFont("Helvetica-Bold", 11)
    c.drawString(mx, y, "Detail Pertemuan")
    y -= 6*mm

    jadwals = data.get("jadwals", [])
    col_no  = 12*mm
    col_tgl = 38*mm
    col_mat = W - mx*2 - col_no - col_tgl

    rows = [["No", "Tanggal", "Materi Pembelajaran"]]
    for i, j in enumerate(jadwals):
        rows.append([
            str(j.get("no", i+1)),
            str(j.get("tanggal", "")),
            str(j.get("materi", "-")),
        ])
    # Minimal 4 baris kosong
    while len(rows) < 5:
        rows.append(["", "", ""])

    t = Table(rows, colWidths=[col_no, col_tgl, col_mat])
    t.setStyle(TableStyle([
        ("BACKGROUND",    (0,0), (-1,0), colors.HexColor("#003d80")),
        ("TEXTCOLOR",     (0,0), (-1,0), colors.white),
        ("FONTNAME",      (0,0), (-1,0), "Helvetica-Bold"),
        ("FONTSIZE",      (0,0), (-1,-1), 9.5),
        ("FONTNAME",      (0,1), (-1,-1), "Helvetica"),
        ("ROWBACKGROUNDS",(0,1), (-1,-1),
            [colors.HexColor("#f9fafb"), colors.white]),
        ("ALIGN",         (0,0), (1,-1), "CENTER"),
        ("VALIGN",        (0,0), (-1,-1), "MIDDLE"),
        ("GRID",          (0,0), (-1,-1), 0.4, colors.HexColor("#d1d5db")),
        ("TOPPADDING",    (0,0), (-1,-1), 5),
        ("BOTTOMPADDING", (0,0), (-1,-1), 5),
        ("LEFTPADDING",   (0,0), (-1,-1), 6),
    ]))
    t.wrapOn(c, sum([col_no, col_tgl, col_mat]), H)
    t_h = t._height
    t.drawOn(c, mx, y - t_h)
    y -= t_h + 9*mm

    # ── Penilaian ─────────────────────────────────────────────
    c.setStrokeColor(colors.HexColor("#e5e7eb"))
    c.line(mx, y, W - mx, y)
    y -= 8*mm

    c.setFillColor(colors.HexColor("#111827"))
    c.setFont("Helvetica-Bold", 11)
    c.drawString(mx, y, "Penilaian")
    y -= 6*mm

    skor = str(data.get("skor", "—"))
    pct  = str(data.get("pct_hadir", "—"))
    th   = str(data.get("total_hadir", "—"))
    ts   = str(data.get("total_sesi", "—"))

    score_data = [
        ["Skor", "Kehadiran", "Hadir", "Total Sesi"],
        [skor,   f"{pct}%",   f"{th}x", f"{ts}x"],
    ]
    sw = [28*mm, 45*mm, 38*mm, 38*mm]
    st = Table(score_data, colWidths=sw)
    st.setStyle(TableStyle([
        ("BACKGROUND",    (0,0), (-1,0), colors.HexColor("#003d80")),
        ("TEXTCOLOR",     (0,0), (-1,0), colors.white),
        ("FONTNAME",      (0,0), (-1,0), "Helvetica-Bold"),
        ("FONTNAME",      (0,1), (-1,-1), "Helvetica-Bold"),
        ("FONTSIZE",      (0,0), (-1,-1), 10),
        ("ALIGN",         (0,0), (-1,-1), "CENTER"),
        ("VALIGN",        (0,0), (-1,-1), "MIDDLE"),
        ("GRID",          (0,0), (-1,-1), 0.4, colors.HexColor("#d1d5db")),
        ("TOPPADDING",    (0,0), (-1,-1), 6),
        ("BOTTOMPADDING", (0,0), (-1,-1), 6),
        # Warnai sel skor
        ("TEXTCOLOR",     (0,1), (0,1), colors.HexColor(skor_color(skor))),
    ]))
    st.wrapOn(c, sum(sw), H)
    sh = st._height
    st.drawOn(c, mx, y - sh)
    y -= sh + 9*mm

    # ── Kesimpulan ────────────────────────────────────────────
    c.setStrokeColor(colors.HexColor("#e5e7eb"))
    c.line(mx, y, W - mx, y)
    y -= 8*mm

    c.setFillColor(colors.HexColor("#111827"))
    c.setFont("Helvetica-Bold", 11)
    c.drawString(mx, y, "Kesimpulan Pembelajaran Bulan " + data.get("bulan_label",""))
    y -= 7*mm

    eval_text = data.get("evaluasi_bulanan") or "Belum ada evaluasi dari guru."
    style = ParagraphStyle(
        "eval", fontName="Helvetica", fontSize=10.5,
        leading=16, textColor=colors.HexColor("#374151"),
    )
    p = Paragraph(eval_text, style)
    pw, ph = p.wrapOn(c, W - mx*2, 300)
    p.drawOn(c, mx, y - ph)
    y -= ph + 12*mm

    # ── TTD ───────────────────────────────────────────────────
    kota = data.get("kota", "Bekasi")
    ttd_label = f"{kota}, {data.get('bulan_label','')}"
    ttd_x = W - mx - 55*mm

    c.setFillColor(colors.HexColor("#374151"))
    c.setFont("Helvetica", 10)
    c.drawString(ttd_x, y, ttd_label)
    y -= 20*mm
    c.setStrokeColor(colors.HexColor("#9ca3af"))
    c.setLineWidth(0.8)
    c.line(ttd_x, y, ttd_x + 50*mm, y)
    y -= 5*mm
    c.setFont("Helvetica-Bold", 10)
    c.drawString(ttd_x, y, data.get("nama_guru", "Guru"))
    y -= 5*mm
    c.setFont("Helvetica", 9.5)
    c.drawString(ttd_x, y, "Guru " + data.get("nama_program", ""))

    # ── Footer ────────────────────────────────────────────────
    c.setFont("Helvetica", 7.5)
    c.setFillColor(colors.HexColor("#9ca3af"))
    c.drawCentredString(W/2, 10*mm,
        "Croma Music · Dokumen ini digenerate otomatis oleh sistem CROMIS")

    c.save()

if __name__ == "__main__":
    payload = json.loads(sys.argv[1])
    out     = sys.argv[2]
    run(payload, out)
    print("OK")