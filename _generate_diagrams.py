#!/usr/bin/env python3
"""Generate BPMN 2.0 .drawio.svg diagrams for Phase X.

Uses a compact DSL to define each process. Each diagram is a list of steps,
auto-laid out horizontally with lanes organizing by profile.
"""

import os, math, textwrap
from datetime import datetime

OUTDIR = 'resources/docs/diagrams'

# ── BPMN palette ──────────────────────────────────────────────
GREEN  = '#d5e8d4'; GREEN_S  = '#82b366'
RED    = '#f8cecc'; RED_S    = '#b85450'
ORANGE = '#ffe6cc'; ORANGE_S = '#d79b00'
BLUE   = '#dae8fc'; BLUE_S   = '#6c8ebf'
YELLOW = '#fff2cc'; YELLOW_S = '#d6b656'
PURPLE = '#e1d5e7'; PURPLE_S = '#9673a6'

PERFIL_COLORS = {
    'veterinario':'#d5e8d4','recepcionista':'#dae8fc','financeiro':'#ffe6cc',
    'estoque':'#fff2cc','sistema':'#f5f5f5','tutor':'#e1d5e7',
    'admin':'#f8cecc','super-admin':'#f8cecc','branch-admin':'#f8cecc',
    'super-financial':'#ffe6cc','rh':'#e1d5e7','auditor':'#e1d5e7',
    'tecnico':'#dae8fc','enfermeiro':'#d5e8d4','radiologista':'#dae8fc',
    'fornecedor':'#fff2cc','vigilancia':'#f8cecc','operadora':'#ffe6cc',
    'github':'#f5f5f5','banco':'#ffe6cc','webmania':'#ffe6cc',
    'clinica':'#e1d5e7','publico':'#f5f5f5','tutor-anonymous':'#f5f5f5',
}

LANE_H = 80
TASK_W = 140; TASK_H = 46
GATE_W = 54;  GATE_H = 54
EVENT_R = 22
SUBP_W = 160; SUBP_H = 54

# ── SVG rendering ─────────────────────────────────────────────

def svg_cell(c):
    x, y, w, h = c['x'], c['y'], c['w'], c['h']
    val = c.get('value','')
    ct = c.get('_ct','task')
    fc = c.get('_fc','#fff')
    sc = c.get('_sc','#000')

    if ct == 'pool':
        return (
            f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="6" ry="6" fill="none" stroke="#000" stroke-width="2"/>'
            f'<rect x="{x}" y="{y}" width="28" height="{h}" fill="{PURPLE}" stroke="#000" stroke-width="2"/>'
            f'<text x="{x+14}" y="{y+h/2}" text-anchor="middle" dominant-baseline="central" fill="#000" font-size="11" font-family="Arial" transform="rotate(-90,{x+14},{y+h/2})">{esc(val)}</text>'
        )
    if ct == 'lane':
        return (
            f'<rect x="{x}" y="{y}" width="{w}" height="{h}" fill="{fc}" stroke="#000" stroke-width="1.5"/>'
            f'<rect x="{x}" y="{y}" width="18" height="{h}" fill="{fc}" stroke="#000" stroke-width="1"/>'
            f'<text x="{x+9}" y="{y+h/2}" text-anchor="middle" dominant-baseline="central" fill="#000" font-size="9" font-family="Arial" transform="rotate(-90,{x+9},{y+h/2})">{esc(val)}</text>'
        )
    if ct == 'start_event':
        return (f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2}" ry="{h/2}" fill="{GREEN}" stroke="{GREEN_S}" stroke-width="2"/>'
                f'<text x="{x+w/2}" y="{y+h+12}" text-anchor="middle" fill="#000" font-size="9" font-family="Arial">{esc(val)}</text>')
    if ct == 'end_event':
        return (f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2}" ry="{h/2}" fill="{RED}" stroke="{RED_S}" stroke-width="2"/>'
                f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2-4}" ry="{h/2-4}" fill="none" stroke="{RED_S}" stroke-width="2"/>'
                f'<text x="{x+w/2}" y="{y+h+12}" text-anchor="middle" fill="#000" font-size="9" font-family="Arial">{esc(val)}</text>')
    if ct == 'intermediate_event':
        return (f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2}" ry="{h/2}" fill="#fff" stroke="#000" stroke-width="2"/>'
                f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2-4}" ry="{h/2-4}" fill="none" stroke="#000" stroke-width="1" stroke-dasharray="2,2"/>'
                f'<text x="{x+w/2}" y="{y+h+12}" text-anchor="middle" fill="#000" font-size="9" font-family="Arial">{esc(val)}</text>')
    if ct in ('gateway_exclusive','xor'):
        return (f'<polygon points="{x+w/2},{y} {x+w},{y+h/2} {x+w/2},{y+h} {x},{y+h/2}" fill="{ORANGE}" stroke="{ORANGE_S}" stroke-width="2"/>'
                f'<text x="{x+w/2}" y="{y+h/2}" text-anchor="middle" dominant-baseline="central" fill="#000" font-size="18" font-family="Arial">✕</text>')
    if ct in ('gateway_parallel','and'):
        return (f'<polygon points="{x+w/2},{y} {x+w},{y+h/2} {x+w/2},{y+h} {x},{y+h/2}" fill="{BLUE}" stroke="{BLUE_S}" stroke-width="2"/>'
                f'<text x="{x+w/2}" y="{y+h/2}" text-anchor="middle" dominant-baseline="central" fill="#000" font-size="18" font-family="Arial">+</text>')
    if ct == 'subprocess':
        return (f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="4" ry="4" fill="#fff" stroke="#000" stroke-width="2"/>'
                f'<rect x="{x+3}" y="{y+3}" width="{w-6}" height="{h-6}" rx="2" ry="2" fill="none" stroke="#000" stroke-width="1"/>'
                f'<text x="{x+w/2}" y="{y+h/2}" text-anchor="middle" dominant-baseline="central" fill="#000" font-size="10" font-family="Arial">{esc(val)}</text>')
    if ct == 'timer':
        return (f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2}" ry="{h/2}" fill="#fff" stroke="#d79b00" stroke-width="2"/>'
                f'<circle cx="{x+w/2}" cy="{y+h/2}" r="6" fill="none" stroke="#d79b00" stroke-width="1"/>'
                f'<text x="{x+w/2}" y="{y+h+12}" text-anchor="middle" fill="#000" font-size="8" font-family="Arial">{esc(val)}</text>')
    # default: task rectangle
    lines = val.split('\\n')
    text_els = []
    for i, line in enumerate(lines):
        ty = y + h/2 - (len(lines)-1)*7 + i*14
        text_els.append(f'<text x="{x+w/2}" y="{ty}" text-anchor="middle" dominant-baseline="central" fill="#000" font-size="10" font-family="Arial">{esc(line)}</text>')
    return (f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="4" ry="4" fill="#fff" stroke="#000" stroke-width="1.5"/>' +
            ''.join(text_els))

def esc(s):
    return s.replace('&','&amp;').replace('<','&lt;').replace('>','&gt;').replace('"','&quot;')

def svg_edge(e, cells):
    src = e.get('_src_cell')
    tgt = e.get('_tgt_cell')
    if not src or not tgt: return ''
    sx, sy = src['x']+src['w']/2, src['y']+src['h']/2
    tx, ty = tgt['x']+tgt['w']/2, tgt['y']+tgt['h']/2
    style = e.get('_style','')
    dashed = 'stroke-dasharray="6,4"' if 'dashed' in style else ''
    label = e.get('label','')
    lbl = ''
    if label:
        mx, my = (sx+tx)/2, (sy+ty)/2
        lbl = f'<text x="{mx}" y="{my-4}" text-anchor="middle" fill="#000" font-size="8" font-family="Arial">{esc(label)}</text>'

    ang = math.atan2(ty-sy, tx-sx)
    a = 6
    ax = tx - a*math.cos(ang)
    ay = ty - a*math.sin(ang)
    arrow = f'{tx},{ty} {ax-4*math.sin(ang)},{ay+4*math.cos(ang)} {ax+4*math.sin(ang)},{ay-4*math.cos(ang)}'
    return f'<line x1="{sx}" y1="{sy}" x2="{tx}" y2="{ty}" stroke="#000" stroke-width="1.5"{dashed}/><polygon points="{arrow}" fill="#000" stroke="#000" stroke-width="1"/>{lbl}'

def gen_drawio(name, pw, ph, cells, edges):
    """Assemble .drawio.svg"""
    # Build mxGraph XML
    mx_root = '<root>'
    mx_root += '<mxCell id="0"/><mxCell id="1" parent="0"/>'
    for c in cells:
        attrs = f'id="{c["id"]}" value="{esc(c.get("value",""))}" style="{esc(c.get("style",""))}" vertex="1" parent="1"'
        mx_root += f'<mxCell {attrs}><mxGeometry x="{c["x"]}" y="{c["y"]}" width="{c["w"]}" height="{c["h"]}" as="geometry"/></mxCell>'
    for e in edges:
        attrs = f'id="{e["id"]}" edge="1" parent="1" source="{e["source"]}" target="{e["target"]}"'
        s = 'edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;'
        if 'dashed' in e.get('_style',''): s += 'dashed=1;'
        attrs += f' style="{esc(s)}"'
        mx_root += f'<mxCell {attrs}><mxGeometry relative="1" as="geometry"/></mxCell>'
    mx_root += '</root>'

    mxfile = (f'<mxfile host="Generator" modified="{datetime.now().isoformat()}" '
              f'agent="BPMN-PHASEX" version="24.0.5" type="device">'
              f'<diagram id="{name}" name="Page-1">'
              f'<mxGraphModel dx="0" dy="0" grid="1" gridSize="10" guides="1" '
              f'page="0" pageWidth="{pw}" pageHeight="{ph}" math="0" shadow="0">'
              f'{mx_root}</mxGraphModel></diagram></mxfile>')

    svg_cells = '\n'.join(svg_cell(c) for c in cells)
    svg_edges = '\n'.join(svg_edge(e, cells) for e in edges)

    return f'''<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="{pw}" height="{ph}" viewBox="0 0 {pw} {ph}">
<switch>
<foreignObject width="{pw}" height="{ph}"><div xmlns="http://www.w3.org/1999/xhtml">{mxfile}</div></foreignObject>
<g transform="translate(0.5,0.5)">{svg_edges}{svg_cells}</g>
</switch>
</svg>'''

# ── Diagram Builder ────────────────────────────────────────────

_counter = [2]
def nid():
    _counter[0] += 1
    return f'c{_counter[0]}'

class Diagram:
    def __init__(self, title, filename, pw=1100, ph=700):
        self.title = title
        self.filename = filename
        self.pw, self.ph = pw, ph
        self.cells = []
        self.edges = []

        # Title
        self._add(10, 5, pw-20, 28, title, 'task',
                  style='text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=14;fontStyle=1')

        self.pools = []       # (name, y, h)
        self.lanes = []       # (name, perfil, y, h, pool_idx)
        self.lane_map = {}    # (pool_name, lane_name) -> lane info

    def add_pool(self, name, lanes_list):
        """lanes_list: [(lane_name, perfil_slug), ...]"""
        # Calculate pool position
        pool_y = 40 + sum(p[2] for p in self.pools) + 15*len(self.pools)
        lane_h = LANE_H
        pool_h = 30 + len(lanes_list)*lane_h + 10
        self.pools.append((name, pool_y, pool_h))

        # Add pool header area (simplified lane)
        y_offset = pool_y + 30
        for lname, perfil in lanes_list:
            fc = PERFIL_COLORS.get(perfil, '#f5f5f5')
            self._add(30, y_offset, self.pw-30, lane_h, lname, 'lane',
                      style=f'swimlane;startSize=18;fillColor={fc};strokeColor=#000;horizontal=1;',
                      perfil=perfil)
            self.lane_map[(name, lname)] = {'y': y_offset, 'h': lane_h, 'perfil': perfil, 'pool': name}
            y_offset += lane_h

        # Pool rectangle
        self._add(0, pool_y, self.pw, pool_h, name, 'pool',
                  style='swimlane;startSize=0;fillColor=none;strokeColor=#000;')

        return self.lanes

    def lane_y(self, pool_name, lane_name):
        info = self.lane_map.get((pool_name, lane_name))
        return info['y'] if info else 100

    def center_y(self, pool_name, lane_name):
        info = self.lane_map.get((pool_name, lane_name))
        return info['y'] + info['h']/2 if info else 120

    def _add(self, x, y, w, h, label, ct, **kw):
        cid = nid()
        style = kw.get('style', '')
        if not style:
            style = 'rounded=1;whiteSpace=wrap;html=1;'
            if ct in ('start_event',):
                style = 'ellipse;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;'
            elif ct in ('end_event',):
                style = 'ellipse;whiteSpace=wrap;html=1;fillColor=#f8cecc;strokeColor=#b85450;'
            elif ct in ('gateway_exclusive','xor'):
                style = 'rhombus;whiteSpace=wrap;html=1;fillColor=#ffe6cc;strokeColor=#d79b00;'
            elif ct in ('gateway_parallel','and'):
                style = 'rhombus;whiteSpace=wrap;html=1;fillColor=#dae8fc;strokeColor=#6c8ebf;'
            elif ct == 'subprocess':
                style = 'rounded=1;whiteSpace=wrap;html=1;fillColor=#fff;strokeColor=#000;container=1;collapsible=0;'
        cell = {'id': cid, 'x': x, 'y': y, 'w': w, 'h': h,
                'value': label, '_ct': ct, 'style': style}
        self.cells.append(cell)
        return {'id': cid}

    def add_task(self, pool, lane, x, label, ct='task'):
        cy = self.center_y(pool, lane)
        tw, th = TASK_W, TASK_H
        if ct in ('gateway_exclusive','xor','gateway_parallel','and'):
            tw, th = GATE_W, GATE_H
        elif ct in ('start_event','end_event','intermediate_event'):
            tw, th = EVENT_R, EVENT_R
        elif ct == 'subprocess':
            tw, th = SUBP_W, SUBP_H
        return self._add(x, cy-th/2, tw, th, label, ct)

    def add_event(self, pool, lane, x, label, ct='start_event'):
        cy = self.center_y(pool, lane)
        return self._add(x, cy-EVENT_R/2, EVENT_R, EVENT_R, label, ct)

    def add_gateway(self, pool, lane, x, label, ct='gateway_exclusive'):
        cy = self.center_y(pool, lane)
        return self._add(x, cy-GATE_H/2, GATE_W, GATE_H, label, ct)

    def add_subprocess(self, pool, lane, x, label):
        cy = self.center_y(pool, lane)
        return self._add(x, cy-SUBP_H/2, SUBP_W, SUBP_H, label, 'subprocess')

    def add_edge(self, sid, tid, style='', label=''):
        eid = nid()
        self.edges.append({'id': eid, 'source': sid, 'target': tid,
                          'label': label, '_style': style})

    def add_message(self, sid, tid, label=''):
        self.add_edge(sid, tid, 'dashed', label)

    def build(self):
        cells = self.cells
        edges = self.edges
        return gen_drawio(self.filename, self.pw, self.ph, cells, edges)

    def save(self):
        svg = self.build()
        os.makedirs(OUTDIR, exist_ok=True)
        fp = os.path.join(OUTDIR, self.filename)
        with open(fp, 'w') as f:
            f.write(svg)
        print(f'  ✓ {self.filename}')

# ── Helper: create a simple left-to-right single-lane diagram ──

def simple_diagram(title, filename, lane_name, perfil, steps, edges_def, pools_extra=None):
    """
    steps: [(label, type), ...]  type='task','start','end','xor','and','subprocess','timer'
    edges_def: [(from_idx, to_idx, label?, style?), ...]
    pools_extra: [(pool_name, lane_name, perfil, [(x, label, type), ...]), ...] for additional pools
    """
    d = Diagram(title, filename, pw=1100)
    d.add_pool(lane_name, [(lane_name, perfil)])
    ids = {}
    x = 50

    for i, (label, ct) in enumerate(steps):
        if ct == 'start':
            ids[i] = d.add_event(lane_name, lane_name, x, label, 'start_event')
            x += 40
        elif ct == 'end':
            ids[i] = d.add_event(lane_name, lane_name, x, label, 'end_event')
            x += 40
        elif ct in ('xor', 'gateway_exclusive'):
            ids[i] = d.add_gateway(lane_name, lane_name, x, label, 'gateway_exclusive')
            x += GATE_W + 40
        elif ct in ('and', 'gateway_parallel'):
            ids[i] = d.add_gateway(lane_name, lane_name, x, label, 'gateway_parallel')
            x += GATE_W + 40
        elif ct == 'timer':
            ids[i] = d.add_event(lane_name, lane_name, x, label, 'timer')
            x += 30
        elif ct == 'subprocess':
            ids[i] = d.add_subprocess(lane_name, lane_name, x, label)
            x += SUBP_W + 30
        else:
            ids[i] = d.add_task(lane_name, lane_name, x, label, 'task')
            x += TASK_W + 30

    for e in edges_def:
        from_i, to_i = e[0], e[1]
        label = e[2] if len(e) > 2 else ''
        style = e[3] if len(e) > 3 else ''
        d.add_edge(ids[from_i], ids[to_i], style, label)

    # Extra pools
    if pools_extra:
        for pname, lname, pperfil, extra_steps in pools_extra:
            d.add_pool(pname, [(lname, pperfil)])
            ex_ids = {}
            for xpos, label, ct in extra_steps:
                if ct.startswith('edge:'):
                    # This is a connection to main lane
                    parts = ct.split(':')
                    from_extra = int(parts[1])
                    to_main = int(parts[2]) if len(parts) > 2 else None
                    if to_main is not None:
                        d.add_message(ex_ids[from_extra], ids[to_main], label)
                    else:
                        d.add_message(ids[from_extra], ex_ids[from_extra], label)
                else:
                    ex_id = d.add_task(pname, lname, xpos, label, ct)
                    ex_ids[len(ex_ids)] = ex_id

    d.save()

# ══════════════════════════════════════════════════════════════
#  DIAGRAM DEFINITIONS
# ══════════════════════════════════════════════════════════════

def x4_1_macro_fluxo():
    """X4.1 — Macro-Fluxo do Sistema (visão geral com 4 pools)"""
    d = Diagram('Macro-Fluxo do Sistema', 'macro-fluxo-sistema.svg', pw=1200, ph=750)
    d.add_pool('Tutor (Portal)', [('Tutor', 'tutor')])
    d.add_pool('Clínica', [('Recepcionista','recepcionista'), ('Veterinário','veterinario'),
                           ('Financeiro','financeiro'), ('Estoque','estoque')])
    d.add_pool('Sistema', [('Sistema','sistema')])

    # Tutor
    t = d.add_task('Tutor (Portal)', 'Tutor', 60, 'Solicita/Cadastra')
    t_rec = d.add_task('Tutor (Portal)', 'Tutor', 300, 'Recebe Notificações')
    t_res = d.add_task('Tutor (Portal)', 'Tutor', 520, 'Visualiza Resultados')

    # Recepcionista
    r_ag = d.add_task('Clínica', 'Recepcionista', 60, 'Agendar Consulta')
    r_cad = d.add_task('Clínica', 'Recepcionista', 300, 'Cadastrar Tutor/Pet')

    # Veterinário
    v_pr = d.add_task('Clínica', 'Veterinário', 60, 'Fazer Prontuário (SOAP)')
    v_gw = d.add_gateway('Clínica', 'Veterinário', 300, '', 'gateway_parallel')
    v_presc = d.add_task('Clínica', 'Veterinário', 440, 'Prescrição')
    v_vac = d.add_task('Clínica', 'Veterinário', 440, 'Vacina')
    v_ex = d.add_task('Clínica', 'Veterinário', 440, 'Exame')
    v_cir = d.add_task('Clínica', 'Veterinário', 440, 'Cirurgia')

    # Financeiro
    f_fat = d.add_task('Clínica', 'Financeiro', 60, 'Faturar Serviços')
    f_gw = d.add_gateway('Clínica', 'Financeiro', 300, 'NFSe?', 'gateway_exclusive')
    f_nfse = d.add_task('Clínica', 'Financeiro', 440, 'NFSe + Comissão')
    f_conc = d.add_task('Clínica', 'Financeiro', 660, 'Conciliação')

    # Estoque
    e_ped = d.add_task('Clínica', 'Estoque', 60, 'Pedido de Compra')
    e_rec = d.add_task('Clínica', 'Estoque', 300, 'Recebimento + Lotes')

    # Sistema
    s_not = d.add_task('Sistema', 'Sistema', 60, 'Disparar Notificações')
    s_lem = d.add_task('Sistema', 'Sistema', 300, 'Lembretes Automáticos')

    start = d.add_event('Clínica', 'Veterinário', 20, 'Início', 'start_event')
    end = d.add_event('Clínica', 'Financeiro', 850, 'Fim', 'end_event')

    # Tutor → Clínica
    d.add_message(t, r_ag, 'agendamento')
    d.add_message(t, r_cad, 'cadastro')

    # Recepcionista → Veterinário
    d.add_edge(r_ag['id'], v_pr['id'])
    d.add_edge(r_cad['id'], v_pr['id'])

    # Start
    d.add_edge(start['id'], r_ag['id'])
    d.add_edge(start['id'], r_cad['id'])

    # Veterinário flow
    d.add_edge(v_pr['id'], v_gw['id'])
    d.add_edge(v_gw['id'], v_presc['id'])
    d.add_edge(v_gw['id'], v_vac['id'])
    d.add_edge(v_gw['id'], v_ex['id'])
    d.add_edge(v_gw['id'], v_cir['id'])

    # To Financeiro
    d.add_edge(v_presc['id'], f_fat['id'])
    d.add_edge(v_vac['id'], f_fat['id'])
    d.add_edge(v_ex['id'], f_fat['id'])
    d.add_edge(v_cir['id'], f_fat['id'])

    # Financeiro flow
    d.add_edge(f_fat['id'], f_gw['id'])
    d.add_edge(f_gw['id'], f_nfse['id'])
    d.add_edge(f_nfse['id'], f_conc['id'])
    d.add_edge(f_conc['id'], end['id'])

    # To System
    d.add_message(v_vac['id'], s_not['id'])
    d.add_edge(s_not['id'], s_lem['id'])

    # System → Tutor
    d.add_message(s_not['id'], t_rec['id'])
    d.add_message(v_ex['id'], t_res['id'])
    d.add_message(s_lem['id'], t_rec['id'])

    # Estoque
    d.add_edge(start['id'], e_ped['id'])
    d.add_edge(e_ped['id'], e_rec['id'])

    d.save()


def x4_2_matriz_perfis():
    """X4.2 — Matriz de Perfis RACI"""
    d = Diagram('Matriz de Perfis (RACI)', 'matriz-perfis.svg', pw=1300, ph=800)

    perfis = [
        ('Super Admin','super-admin'), ('Admin','admin'),
        ('Branch Admin','branch-admin'), ('Veterinário','veterinario'),
        ('Recepcionista','recepcionista'), ('Financeiro','financeiro'),
        ('Super Financial','super-financial'), ('Estoque','estoque'),
        ('RH','rh'), ('Tutor','tutor'), ('Auditor','auditor'),
    ]

    col_w = 100
    sx = 160
    header_y = 40

    from functools import cmp_to_key

    for i, (name, slug) in enumerate(perfis):
        fc = PERFIL_COLORS.get(slug, '#f5f5f5')
        d._add(sx+i*col_w, header_y, col_w, 28, name, 'task',
               style=f'text;html=1;strokeColor=#000;fillColor={fc};align=center;fontSize=9;fontStyle=1;whiteSpace=wrap;overflow=hidden;')

    # Module column header
    d._add(10, header_y, 140, 28, 'Módulo / Funcionalidade', 'task',
           style='text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=10;fontStyle=1;')

    RACI = [
        ('Macro-Fluxo',    ['I','I','I','R','C','C','','C','','C','']),
        ('Prontuário',     ['','','','R','','','','','','C','']),
        ('Prescrição',     ['','','','R','','','','','','C','']),
        ('Vacina',         ['','','','R','C','','','','','C','']),
        ('Exame',          ['','','','R','C','','','','','C','']),
        ('Cirurgia',       ['','','','R','C','','','','','C','']),
        ('Internação',     ['','','','R','','','','','','C','']),
        ('Farmácia',       ['','','','C','','','','R','','','']),
        ('Estoque',        ['A','A','A','','','C','','R','','','']),
        ('Financeiro',     ['','','','','','R','A','','','','']),
        ('Conciliação',    ['','','','','','R','C','','','','']),
        ('NFSe',           ['','','','','','R','','','','C','']),
        ('Agendamento',    ['','','','C','R','','','','','C','']),
        ('Tutor/Pet',      ['','','','','R','','','','','C','']),
        ('Convênio',       ['','','','C','','R','','','','C','']),
        ('LGPD',           ['','','','','','','','','','C','R']),
        ('Notificações',   ['','','','','','','','','','C','']),
        ('Chat',           ['','','','C','C','','','','','R','']),
        ('Auto-Update',    ['R','C','','','','','','','','','']),
        ('Emergência',     ['','','','R','','','','','','C','']),
        ('Triagem',        ['','','','R','C','','','','','C','']),
        ('Hospedagem',     ['','','','C','R','','','','','C','']),
        ('Odontologia',    ['','','','R','','','','','','C','']),
        ('Zoonoses',       ['','','','R','','','','','','C','']),
        ('RH',             ['A','A','C','','','','','','R','','']),
        ('Relatórios',     ['A','A','A','R','R','R','','R','R','','A']),
        ('Mobile',         ['','','','R','','','','','','','']),
    ]

    rh = 24
    for ri, (mod, ratings) in enumerate(RACI):
        ry = header_y + 38 + ri*rh
        d._add(10, ry, 140, rh, mod, 'task',
               style=f'text;html=1;strokeColor=none;fillColor=none;align=right;fontSize=9;')
        for ci, r in enumerate(ratings):
            if not r: continue
            cx = sx + ci*col_w
            fc = {'R':'#d5e8d4','A':'#f8cecc','C':'#dae8fc','I':'#fff2cc'}[r]
            d._add(cx, ry, col_w, rh, r, 'task',
                   style=f'text;html=1;strokeColor=#ccc;fillColor={fc};align=center;fontSize=9;fontWeight=bold;')

    ly = header_y + 38 + len(RACI)*rh + 20
    for dx, txt, fc in [(150,'R = Responsável','#d5e8d4'),(350,'A = Aprovador','#f8cecc'),
                         (550,'C = Consultado','#dae8fc'),(750,'I = Informado','#fff2cc')]:
        d._add(dx, ly, 180, 22, txt, 'task',
               style=f'text;html=1;strokeColor=#000;fillColor={fc};align=center;fontSize=10;')

    d.save()


# ── Process diagrams ──────────────────────────────────────────

def x4_3_prontuario():
    d = Diagram('Fluxo de Prontuário (SOAP)', '05-fluxo-prontuario.svg', pw=1100, ph=400)
    d.add_pool('Clínica', [('Veterinário','veterinario')])
    d.add_pool('Tutor (Portal)', [('Tutor','tutor')])

    v = 'Clínica', 'Veterinário'
    t = 'Tutor (Portal)', 'Tutor'

    start = d.add_event(*v, 40, 'Abrir ficha do pet', 'start_event')

    soap = d.add_task(*v, 120, 'Preencher SOAP\n(S+O+A+P)')
    anexos = d.add_task(*v, 310, 'Adicionar\nAnexos', 'subprocess')
    gw1 = d.add_gateway(*v, 520, 'Plano de\nTratamento?', 'gateway_exclusive')
    plano = d.add_subprocess(*v, 650, 'Plano de\nTratamento')
    aprova = d.add_task(*t, 650, 'Aprovar/Rejeitar\nPlano')
    gw2 = d.add_gateway(*v, 850, 'Aprovado?', 'gateway_exclusive')
    exec_plano = d.add_task(*v, 970, 'Executar\nPlano')
    presc = d.add_task(*v, 650, 'Prescrever\n(se necessário)')
    end = d.add_event(*v, 970, 'Prontuário\nSalvo', 'end_event')
    notif1 = d.add_event(*v, 520, 'Notificação\nao Tutor', 'intermediate_event')

    d.add_edge(start['id'], soap['id'])
    d.add_edge(soap['id'], anexos['id'])
    d.add_edge(anexos['id'], gw1['id'])
    d.add_edge(gw1['id'], plano['id'])
    d.add_message(plano['id'], aprova['id'], 'aprovação pendente')
    d.add_edge(aprova['id'], gw2['id'])
    d.add_edge(gw2['id'], exec_plano['id'])
    d.add_message(gw2['id'], presc['id'], 'rejeitado → ajustar')
    d.add_edge(exec_plano['id'], end['id'])
    d.add_message(plano['id'], notif1['id'])
    d.add_edge(notif1['id'], gw1['id'])
    d.save()


def x4_4_prescricao():
    d = Diagram('Fluxo de Prescrição com QR Code', '06-fluxo-prescricao.svg', pw=1100, ph=450)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])
    d.add_pool('Público', [('Público','publico')])

    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'
    p = 'Público', 'Público'

    start = d.add_event(*v, 40, 'Selecionar Pet', 'start_event')
    add_med = d.add_task(*v, 120, 'Adicionar Medicamentos\n(fármaco, dose, frequência)')
    gw = d.add_gateway(*v, 330, 'Substância\nControlada?', 'gateway_exclusive')
    val_anvisa = d.add_task(*v, 480, 'Validar ANVISA\n(Azul/Amarelo)')
    hash_qr = d.add_event(*s, 480, 'Gerar Hash\nSHA-256 + QR', 'intermediate_event')
    pdf = d.add_task(*v, 680, 'Gerar PDF\nFormatado')
    notif = d.add_event(*s, 680, 'Notificar\nTutor', 'intermediate_event')
    rec = d.add_task(*t, 680, 'Receber PDF')
    scan = d.add_task(*p, 480, 'Escaneia QR Code')
    verif = d.add_task(*p, 680, 'Verifica\n/r/{hash}')

    end = d.add_event(*v, 900, 'Prescrição\nSalva', 'end_event')

    d.add_edge(start['id'], add_med['id'])
    d.add_edge(add_med['id'], gw['id'])
    d.add_edge(gw['id'], val_anvisa['id'])
    d.add_edge(val_anvisa['id'], hash_qr['id'])
    d.add_edge(hash_qr['id'], pdf['id'])
    d.add_edge(pdf['id'], end['id'])
    d.add_message(pdf['id'], notif['id'])
    d.add_message(notif['id'], rec['id'])
    d.add_message(rec['id'], scan['id'])
    d.add_edge(scan['id'], verif['id'])
    d.save()


def x4_5_vacina():
    d = Diagram('Fluxo de Vacinação', '07-fluxo-vacina.svg', pw=1200, ph=500)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Recepcionista','recepcionista'), ('Sistema','sistema')])
    d.add_pool('Tutor (Portal)', [('Tutor','tutor')])

    v = 'Clínica', 'Veterinário'
    r = 'Clínica', 'Recepcionista'
    s = 'Clínica', 'Sistema'
    t = 'Tutor (Portal)', 'Tutor'

    start = d.add_event(*v, 40, 'Selecionar\nPet+Vacina', 'start_event')
    gw_prot = d.add_gateway(*v, 200, 'Protocolo\nAtivo?', 'gateway_exclusive')
    sugere = d.add_task(*v, 350, 'Sugerir Vacina\nDose+Intervalo')
    manual = d.add_task(*v, 350, 'Lançar\nManual')
    lote = d.add_task(*r, 530, 'Informar Lote\n+ Validade')
    aplica = d.add_task(*v, 710, 'Aplicar Vacina')
    cert = d.add_task(*s, 710, 'Gerar Certificado\nPDF (CFMV)')
    rec_notif = d.add_task(*s, 900, 'Agendar Próx.\nLembrete')
    tutor_rec = d.add_task(*t, 900, 'Recebe\nCertificado')

    end = d.add_event(*v, 1050, 'Vacina\nRegistrada', 'end_event')

    d.add_edge(start['id'], gw_prot['id'])
    d.add_edge(gw_prot['id'], sugere['id'])
    d.add_edge(gw_prot['id'], manual['id'])
    d.add_edge(sugere['id'], lote['id'])
    d.add_edge(manual['id'], lote['id'])
    d.add_edge(lote['id'], aplica['id'])
    d.add_edge(aplica['id'], cert['id'])
    d.add_edge(cert['id'], rec_notif['id'])
    d.add_message(rec_notif['id'], tutor_rec['id'])
    d.add_edge(rec_notif['id'], end['id'])
    d.save()


def x4_6_exame():
    d = Diagram('Fluxo de Exame', '08-fluxo-exame.svg', pw=1100, ph=450)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Recepcionista','recepcionista'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    v = 'Clínica', 'Veterinário'
    r = 'Clínica', 'Recepcionista'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*v, 40, 'Solicitar\nExame', 'start_event')
    gw_tipo = d.add_gateway(*v, 200, 'Tipo?', 'gateway_parallel')
    lab = d.add_subprocess(*s, 370, 'Laboratório\n(coleta+laudo)')
    img = d.add_subprocess(*s, 370, 'Imagem\n(laudo+assinatura)')
    result = d.add_event(*s, 600, 'Resultado\nLiberado', 'intermediate_event')
    tutor_view = d.add_task(*t, 600, 'Visualizar\nResultado')
    end = d.add_event(*v, 800, 'Fim', 'end_event')

    d.add_edge(start['id'], gw_tipo['id'])
    d.add_edge(gw_tipo['id'], lab['id'])
    d.add_edge(gw_tipo['id'], img['id'])
    d.add_edge(lab['id'], result['id'])
    d.add_edge(img['id'], result['id'])
    d.add_message(result['id'], tutor_view['id'])
    d.add_edge(result['id'], end['id'])
    d.save()


def x4_7_laboratorio():
    d = Diagram('Fluxo de Laboratório', '09-fluxo-laboratorio.svg', pw=1100, ph=450)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Técnico','tecnico'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])
    d.add_pool('Equipamento', [('Equipamento','sistema')])

    vt = 'Clínica', 'Veterinário'
    tc = 'Clínica', 'Técnico'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'
    eq = 'Equipamento', 'Equipamento'

    start = d.add_event(*vt, 40, 'Pedido de\nExame', 'start_event')
    coleta = d.add_task(*tc, 200, 'Registrar\nColeta')
    gw = d.add_gateway(*s, 370, 'Equipamento\nIntegrado?', 'gateway_exclusive')
    auto = d.add_task(*eq, 530, 'Webhook HL7\nImportação')
    manual = d.add_task(*tc, 530, 'Lançamento\nManual')
    laudo = d.add_subprocess(*s, 700, 'Laudo +\nConclusão')
    libera = d.add_event(*s, 900, 'Resultado\nLiberado', 'intermediate_event')
    tutor_v = d.add_task(*t, 900, 'Visualiza\nPortal')
    end = d.add_event(*vt, 1050, 'Fim', 'end_event')

    d.add_edge(start['id'], coleta['id'])
    d.add_edge(coleta['id'], gw['id'])
    d.add_edge(gw['id'], auto['id'])
    d.add_edge(gw['id'], manual['id'])
    d.add_edge(auto['id'], laudo['id'])
    d.add_edge(manual['id'], laudo['id'])
    d.add_edge(laudo['id'], libera['id'])
    d.add_message(libera['id'], tutor_v['id'])
    d.add_edge(libera['id'], end['id'])
    d.save()


def x4_8_imagem():
    d = Diagram('Fluxo de Imagem (Raio-X, US, Tomografia)', '10-fluxo-imagem.svg', pw=1100, ph=350)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Radiologista','radiologista')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    vt = 'Clínica', 'Veterinário'
    rd = 'Clínica', 'Radiologista'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*vt, 40, 'Solicitar\nExame de Imagem', 'start_event')
    upload = d.add_task(*rd, 250, 'Upload DICOM\n+ Região Anatômica')
    laudo = d.add_task(*rd, 450, 'Redigir Laudo\n(descrição+conclusão)')
    assina = d.add_event(*rd, 650, 'Assinatura\nDigital', 'intermediate_event')
    associar = d.add_task(*vt, 800, 'Associar ao\nProntuário')
    tutor_v = d.add_task(*t, 450, 'Visualiza\nResultado')
    end = d.add_event(*vt, 1000, 'Fim', 'end_event')

    d.add_edge(start['id'], upload['id'])
    d.add_edge(upload['id'], laudo['id'])
    d.add_edge(laudo['id'], assina['id'])
    d.add_edge(assina['id'], associar['id'])
    d.add_message(assina['id'], tutor_v['id'])
    d.add_edge(associar['id'], end['id'])
    d.save()


def x4_9_cirurgia():
    d = Diagram('Fluxo de Cirurgia', '11-fluxo-cirurgia.svg', pw=1200, ph=500)
    d.add_pool('Clínica', [('Recepcionista','recepcionista'), ('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    r = 'Clínica', 'Recepcionista'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*r, 40, 'Recepcionista\nAgenda', 'start_event')
    gw_check = d.add_gateway(*r, 250, 'Pré-OP\nChecklist', 'gateway_parallel')
    aval = d.add_task(*v, 420, 'Avaliação\nPré-Anestésica')
    consent = d.add_task(*t, 420, 'Termo de\nConsentimento')
    checklist = d.add_task(*v, 420, 'Checklist\nCirúrgico')
    gw_ok = d.add_gateway(*v, 620, 'Todos OK?', 'gateway_exclusive')
    cirurgia = d.add_task(*v, 780, 'Realizar\nCirurgia')
    transop = d.add_task(*v, 950, 'Registrar\nTransoperatório')
    posop = d.add_task(*v, 950, 'Pós-OP\n+ Prescrição')
    notif = d.add_event(*s, 950, 'Notificar\nTutor', 'intermediate_event')
    end = d.add_event(*v, 1100, 'Alta', 'end_event')

    d.add_edge(start['id'], gw_check['id'])
    d.add_edge(gw_check['id'], aval['id'])
    d.add_edge(gw_check['id'], consent['id'])
    d.add_edge(gw_check['id'], checklist['id'])
    d.add_edge(aval['id'], gw_ok['id'])
    d.add_edge(consent['id'], gw_ok['id'])
    d.add_edge(checklist['id'], gw_ok['id'])
    d.add_edge(gw_ok['id'], cirurgia['id'])
    d.add_edge(cirurgia['id'], transop['id'])
    d.add_edge(cirurgia['id'], posop['id'])
    d.add_edge(posop['id'], notif['id'])
    d.add_edge(transop['id'], end['id'])
    d.add_message(notif['id'], consent['id'])
    d.save()


def x4_10_internacao():
    d = Diagram('Fluxo de Internação', '12-fluxo-internacao.svg', pw=1200, ph=500)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Enfermeiro','enfermeiro'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    v = 'Clínica', 'Veterinário'
    e = 'Clínica', 'Enfermeiro'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*v, 40, 'Registrar\nInternação', 'start_event')
    notif_adm = d.add_event(*s, 200, 'Notificar\nTutor', 'intermediate_event')
    ciclo = d.add_task(*e, 370, 'Ciclo: Evolução\nDiária (sinais vitais)')
    presc_dia = d.add_task(*v, 370, 'Prescrição\nDiária')
    gw_alta = d.add_gateway(*v, 570, 'Alta\nMédica?', 'gateway_exclusive')
    paralelo = d.add_gateway(*v, 740, '', 'gateway_parallel')
    resumo = d.add_task(*v, 900, 'Resumo de\nAlta')
    presc_alta = d.add_task(*v, 900, 'Prescrição\nde Alta')
    orient = d.add_task(*v, 900, 'Orientações')
    retorno = d.add_task(*s, 900, 'Agendar\nRetorno')
    notif_alta = d.add_event(*s, 1080, 'Notificar\nTutor', 'intermediate_event')
    tutor_not = d.add_task(*t, 200, 'Recebe\nNotificações')
    end = d.add_event(*v, 1150, 'Fim', 'end_event')

    d.add_edge(start['id'], notif_adm['id'])
    d.add_message(notif_adm['id'], tutor_not['id'])
    d.add_edge(notif_adm['id'], ciclo['id'])
    d.add_edge(ciclo['id'], presc_dia['id'])
    d.add_edge(presc_dia['id'], gw_alta['id'])
    d.add_edge(gw_alta['id'], ciclo['id'])  # loop back
    d.add_edge(gw_alta['id'], paralelo['id'])
    d.add_edge(paralelo['id'], resumo['id'])
    d.add_edge(paralelo['id'], presc_alta['id'])
    d.add_edge(paralelo['id'], orient['id'])
    d.add_edge(paralelo['id'], retorno['id'])
    d.add_edge(resumo['id'], notif_alta['id'])
    d.add_edge(presc_alta['id'], notif_alta['id'])
    d.add_edge(orient['id'], notif_alta['id'])
    d.add_edge(retorno['id'], notif_alta['id'])
    d.add_message(notif_alta['id'], tutor_not['id'])
    d.add_edge(notif_alta['id'], end['id'])
    d.save()


def x4_11_farmacia():
    d = Diagram('Fluxo de Farmácia', '13-fluxo-farmacia.svg', pw=1100, ph=400)
    d.add_pool('Clínica', [('Estoque','estoque'), ('Veterinário','veterinario'), ('Sistema','sistema')])

    e = 'Clínica', 'Estoque'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'

    start = d.add_event(*e, 40, 'Cadastrar\nProduto', 'start_event')
    gw_ctrl = d.add_gateway(*e, 220, 'Controlado\nANVISA?', 'gateway_exclusive')
    marca = d.add_task(*e, 370, 'Marcar como\nControlado')
    lote = d.add_task(*e, 530, 'Registrar\nLote+Validade')
    gw_preco = d.add_gateway(*e, 700, 'Preço por\nEspécie?', 'gateway_exclusive')
    tiers = d.add_task(*e, 850, 'Configurar\nTiers')
    gw_uso = d.add_gateway(*e, 1000, 'Venda ou\nUso Clínico?', 'gateway_exclusive')
    venda = d.add_task(*e, 1100, 'Debitar\nEstoque')
    uso = d.add_task(*v, 1100, 'Consumo\nno Prontuário')
    end = d.add_event(*e, 1200, 'Fim', 'end_event')

    d.add_edge(start['id'], gw_ctrl['id'])
    d.add_edge(gw_ctrl['id'], marca['id'])
    d.add_edge(marca['id'], lote['id'])
    d.add_edge(gw_ctrl['id'], lote['id'])
    d.add_edge(lote['id'], gw_preco['id'])
    d.add_edge(gw_preco['id'], tiers['id'])
    d.add_edge(gw_preco['id'], gw_uso['id'])
    d.add_edge(tiers['id'], gw_uso['id'])
    d.add_edge(gw_uso['id'], venda['id'])
    d.add_edge(gw_uso['id'], uso['id'])
    d.add_edge(venda['id'], end['id'])
    d.add_edge(uso['id'], end['id'])
    d.save()


def x4_12_estoque():
    d = Diagram('Fluxo de Estoque e Pedidos de Compra', '14-fluxo-estoque.svg', pw=1200, ph=500)
    d.add_pool('Clínica', [('Estoque','estoque'), ('Admin','admin'), ('Financeiro','financeiro')])
    d.add_pool('Fornecedor', [('Fornecedor','fornecedor')])

    e = 'Clínica', 'Estoque'
    a = 'Clínica', 'Admin'
    f = 'Clínica', 'Financeiro'
    forn = 'Fornecedor', 'Fornecedor'

    start = d.add_event(*e, 40, 'Criar Pedido\n(draft)', 'start_event')
    itens = d.add_task(*e, 220, 'Adicionar\nItens+Qtd+Preço')
    gw_valor = d.add_gateway(*e, 420, 'Valor >\nLimite?', 'gateway_exclusive')
    aprov = d.add_task(*a, 600, 'Aprovação\nNecessária')
    gw_apr = d.add_gateway(*a, 780, 'Aprovado?', 'gateway_exclusive')
    rej = d.add_task(*e, 780, 'Rejeitado\n(volta a draft)')
    envio = d.add_task(*e, 960, 'Enviar ao\nFornecedor')
    forn_rec = d.add_task(*forn, 960, 'Receber\nPedido')
    rec_parc = d.add_gateway(*e, 1140, 'Receb.\nParcial?', 'gateway_exclusive')
    concil = d.add_task(*f, 1300, 'Conciliação\n(confere valores)')
    end = d.add_event(*e, 1400, 'Pedido\nFinalizado', 'end_event')

    d.add_edge(start['id'], itens['id'])
    d.add_edge(itens['id'], gw_valor['id'])
    d.add_edge(gw_valor['id'], aprov['id'])
    d.add_edge(gw_valor['id'], envio['id'])
    d.add_edge(aprov['id'], gw_apr['id'])
    d.add_edge(gw_apr['id'], envio['id'])
    d.add_edge(gw_apr['id'], rej['id'])
    d.add_edge(rej['id'], itens['id'])  # back
    d.add_edge(envio['id'], forn_rec['id'])
    d.add_edge(forn_rec['id'], rec_parc['id'])
    d.add_edge(rec_parc['id'], concil['id'])
    d.add_edge(rec_parc['id'], concil['id'])
    d.add_edge(concil['id'], end['id'])
    d.save()


def x4_12b_substancias():
    d = Diagram('Fluxo de Substâncias Controladas', '14-fluxo-substancias.svg', pw=900, ph=400)
    d.add_pool('Clínica', [('Estoque','estoque'), ('Veterinário','veterinario'), ('Sistema','sistema')])

    e = 'Clínica', 'Estoque'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'

    start = d.add_event(*e, 40, 'Compra\nControlada', 'start_event')
    reg = d.add_task(*e, 200, 'Registrar Entrada\nLote+Validade+Qtd')
    audit = d.add_task(*v, 400, 'Saída Auditada\n(quem/pet/prescrição)')
    rel_mensal = d.add_task(*s, 600, 'Relatório\nMensal ANVISA')
    rel_anual = d.add_task(*s, 600, 'Relatório\nAnual')
    end = d.add_event(*e, 780, 'Fim', 'end_event')

    d.add_edge(start['id'], reg['id'])
    d.add_edge(reg['id'], audit['id'])
    d.add_edge(audit['id'], rel_mensal['id'])
    d.add_edge(audit['id'], rel_anual['id'])
    d.add_edge(rel_mensal['id'], end['id'])
    d.add_edge(rel_anual['id'], end['id'])
    d.save()


def x4_13_fatura():
    d = Diagram('Fluxo de Faturamento, NFSe e Comissões', '15-fluxo-fatura.svg', pw=1300, ph=600)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Financeiro','financeiro'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])
    d.add_pool('Webmania®', [('Webmania','webmania')])
    d.add_pool('Banco', [('Banco','banco')])

    v = 'Clínica', 'Veterinário'
    fi = 'Clínica', 'Financeiro'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'
    web = 'Webmania®', 'Webmania'
    banco = 'Banco', 'Banco'

    start = d.add_event(*fi, 40, 'Fatura\nGerada', 'start_event')
    itens = d.add_task(*fi, 200, 'Adicionar\nItens/Serviços')
    pagto = d.add_task(*t, 200, 'Pagar\n(Dinheiro/Cartão/PIX)')
    gw_pago = d.add_gateway(*fi, 400, 'Pago?', 'gateway_exclusive')
    event = d.add_event(*s, 580, 'InvoicePaid\n(evento)', 'intermediate_event')
    paralelo = d.add_gateway(*s, 750, '', 'gateway_parallel')

    # ① NFSe
    gw_nfse = d.add_gateway(*fi, 900, 'NFSe\nConfig?', 'gateway_exclusive')
    emitir = d.add_task(*fi, 1050, 'Emitir NFSe')
    web_nfse = d.add_task(*web, 1050, 'Processar\nNFSe')
    guarda = d.add_task(*s, 1050, 'Salvar XML\n+ PDF')
    email = d.add_task(*t, 1230, 'Receber NFSe\npor E-mail')

    # ② Comissão
    gw_com = d.add_gateway(*fi, 900, 'Comissão\nConfig?', 'gateway_exclusive')
    calc = d.add_task(*fi, 1050, 'Calcular\nComissão')
    pend = d.add_task(*fi, 1230, 'Commission\npending')
    paga = d.add_task(*fi, 1380, 'Financeiro\npaga')

    # ③ Conciliação
    gw_conc = d.add_gateway(*fi, 900, 'Conciliação\nAutomática?', 'gateway_exclusive')
    trans = d.add_task(*banco, 1050, 'Transação\nBancária')
    recon = d.add_task(*s, 1050, 'Reconcilied')

    end = d.add_event(*fi, 1500, 'Fim', 'end_event')

    d.add_edge(start['id'], itens['id'])
    d.add_edge(itens['id'], pagto['id'])
    d.add_edge(pagto['id'], gw_pago['id'])
    d.add_edge(gw_pago['id'], event['id'])
    d.add_edge(event['id'], paralelo['id'])

    d.add_edge(paralelo['id'], gw_nfse['id'])
    d.add_edge(gw_nfse['id'], emitir['id'])
    d.add_edge(emitir['id'], web_nfse['id'])
    d.add_edge(web_nfse['id'], guarda['id'])
    d.add_message(guarda['id'], email['id'])

    d.add_edge(paralelo['id'], gw_com['id'])
    d.add_edge(gw_com['id'], calc['id'])
    d.add_edge(calc['id'], pend['id'])
    d.add_edge(pend['id'], paga['id'])

    d.add_edge(paralelo['id'], gw_conc['id'])
    d.add_edge(gw_conc['id'], trans['id'])
    d.add_edge(trans['id'], recon['id'])

    d.add_edge(emitir['id'], end['id'])
    d.add_edge(paga['id'], end['id'])
    d.add_edge(recon['id'], end['id'])
    d.save()


def x4_13b_conciliacao():
    d = Diagram('Fluxo de Conciliação Bancária', '15-fluxo-conciliacao.svg', pw=1100, ph=450)
    d.add_pool('Clínica', [('Financeiro','financeiro'), ('Super-Financial','super-financial'), ('Sistema','sistema')])
    d.add_pool('Banco', [('Banco','banco')])

    fi = 'Clínica', 'Financeiro'
    sf = 'Clínica', 'Super-Financial'
    s = 'Clínica', 'Sistema'
    b = 'Banco', 'Banco'

    start = d.add_event(*sf, 40, 'Importar\nExtrato', 'start_event')
    importa = d.add_task(*b, 200, 'OFX/QIF/CSV')
    processa = d.add_task(*s, 200, 'Processar\nTransações')
    sugere = d.add_task(*s, 420, 'Sugerir\nCorrespondências')
    gw_auto = d.add_gateway(*sf, 620, 'Match\nAutomático?', 'gateway_exclusive')
    auto_ok = d.add_task(*s, 800, 'Reconcilied\nAutomático')
    manual = d.add_task(*fi, 800, 'Arrastar\n+ Confirmar')
    end = d.add_event(*sf, 1000, 'Fim', 'end_event')

    d.add_edge(start['id'], importa['id'])
    d.add_edge(importa['id'], processa['id'])
    d.add_edge(processa['id'], sugere['id'])
    d.add_edge(sugere['id'], gw_auto['id'])
    d.add_edge(gw_auto['id'], auto_ok['id'])
    d.add_edge(gw_auto['id'], manual['id'])
    d.add_edge(auto_ok['id'], end['id'])
    d.add_edge(manual['id'], end['id'])
    d.save()


def x4_14_agendamento():
    d = Diagram('Fluxo de Agendamento e Consulta', '16-fluxo-agendamento.svg', pw=1200, ph=550)
    d.add_pool('Tutor (Portal)', [('Tutor','tutor')])
    d.add_pool('Clínica', [('Recepcionista','recepcionista'), ('Veterinário','veterinario'), ('Sistema','sistema')])

    t = 'Tutor (Portal)', 'Tutor'
    r = 'Clínica', 'Recepcionista'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'

    gw_orig = d.add_gateway(*s, 40, 'Origem?', 'gateway_exclusive')
    portal = d.add_task(*t, 200, 'Acessa Portal\nPet+Serviço+Horário')
    presencial = d.add_task(*r, 200, 'Calendário\n+ Dados Tutor')
    pending = d.add_event(*s, 400, 'pending', 'intermediate_event')
    gw_confirm = d.add_gateway(*r, 550, 'Tutor\nConfirmou?', 'gateway_exclusive')
    confirmed = d.add_task(*s, 720, 'confirmed')
    lembrete24 = d.add_event(*s, 720, 'Lembrete\n24h', 'timer')
    lembrete2 = d.add_event(*s, 720, 'Lembrete\n2h', 'timer')
    consulta = d.add_task(*v, 900, 'Consulta\nRealizada')
    gw_conc = d.add_gateway(*v, 1080, 'Concluída?', 'gateway_exclusive')
    fatura = d.add_task(*s, 1200, 'Auto-\nFaturamento')
    end = d.add_event(*r, 1350, 'Fim', 'end_event')

    d.add_edge(gw_orig['id'], portal['id'])
    d.add_edge(gw_orig['id'], presencial['id'])
    d.add_edge(portal['id'], pending['id'])
    d.add_edge(presencial['id'], pending['id'])
    d.add_edge(pending['id'], gw_confirm['id'])
    d.add_edge(gw_confirm['id'], confirmed['id'])
    d.add_event(*s, 720, 'Reagenda', 'intermediate_event')
    d.add_edge(confirmed['id'], lembrete24['id'])
    d.add_edge(lembrete24['id'], lembrete2['id'])
    d.add_edge(lembrete2['id'], consulta['id'])
    d.add_edge(consulta['id'], gw_conc['id'])
    d.add_edge(gw_conc['id'], fatura['id'])
    d.add_edge(fatura['id'], end['id'])
    d.save()


def x4_15_tutor_pet():
    d = Diagram('Fluxo de Cadastro de Tutor e Pet', '17-fluxo-tutor-pet.svg', pw=1100, ph=450)
    d.add_pool('Clínica', [('Recepcionista','recepcionista'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    r = 'Clínica', 'Recepcionista'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*r, 40, 'Cadastrar\nTutor', 'start_event')
    dados = d.add_task(*r, 200, 'Nome+CPF+Email\n+Telefone+End.')
    valida = d.add_task(*s, 200, 'CPF Único\n+ Validação')
    pref = d.add_task(*r, 400, 'Preferências de\nNotificação')
    pet = d.add_task(*r, 580, 'Cadastrar\nPet (nome, esp, raça)')
    gw_micro = d.add_gateway(*r, 780, 'Microchip?', 'gateway_exclusive')
    micro = d.add_task(*r, 940, 'Registrar\nMicrochip')
    rg = d.add_task(*r, 940, 'RG Animal\n+ Órgão')
    gw_tutor = d.add_gateway(*r, 1100, 'Vínculos\nAdicionais?', 'gateway_exclusive')
    vinculos = d.add_task(*r, 1250, 'Adicionar\nVínculos')
    timeline = d.add_task(*s, 1250, 'Timeline do\nPaciente')
    end = d.add_event(*r, 1400, 'Fim', 'end_event')

    d.add_edge(start['id'], dados['id'])
    d.add_edge(dados['id'], valida['id'])
    d.add_edge(valida['id'], pref['id'])
    d.add_edge(pref['id'], pet['id'])
    d.add_edge(pet['id'], gw_micro['id'])
    d.add_edge(gw_micro['id'], micro['id'])
    d.add_edge(gw_micro['id'], rg['id'])
    d.add_edge(micro['id'], gw_tutor['id'])
    d.add_edge(rg['id'], gw_tutor['id'])
    d.add_edge(gw_tutor['id'], vinculos['id'])
    d.add_edge(gw_tutor['id'], timeline['id'])
    d.add_edge(vinculos['id'], timeline['id'])
    d.add_edge(timeline['id'], end['id'])
    d.save()


def x4_16_convenio():
    d = Diagram('Fluxo de Convênio, Claims e CVI', '18-fluxo-convenio.svg', pw=1300, ph=600)
    d.add_pool('Clínica', [('Financeiro','financeiro'), ('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])
    d.add_pool('Operadora', [('Operadora','operadora')])

    fi = 'Clínica', 'Financeiro'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'
    op = 'Operadora', 'Operadora'

    start = d.add_event(*fi, 40, 'Cadastrar\nConvênio', 'start_event')
    tabela = d.add_task(*fi, 200, 'Tabela de\nProcedimentos')
    atend = d.add_task(*v, 200, 'Atendimento\nRealizado')
    gw_conv = d.add_gateway(*fi, 400, 'Pet é\nConveniado?', 'gateway_exclusive')
    guia = d.add_task(*fi, 580, 'Faturamento\nde Guia')
    lote = d.add_task(*fi, 580, 'Lote de\nFaturamento')
    envia = d.add_task(*s, 780, 'Enviar ao\nConvênio')
    proc = d.add_task(*op, 780, 'Processar\nGuia')
    status = d.add_task(*fi, 960, 'Status:\nPago/Glosado/Pendente')

    # Claims
    gw_claim = d.add_gateway(*fi, 580, 'Auto-Claim\nAtivo?', 'gateway_exclusive')
    claim = d.add_task(*s, 780, 'claims:auto-file\nAPI Porto Seguro')
    webhook = d.add_task(*op, 780, 'Webhook\nPOST /api/...')
    atualiza = d.add_task(*s, 960, 'Atualizar\nStatus')

    # CVI
    cvi_start = d.add_task(*v, 580, 'Solicitar\nCVI')
    gw_req = d.add_gateway(*v, 780, 'Requisitos\nOK?', 'gateway_exclusive')
    gera_cvi = d.add_task(*s, 960, 'Gerar CVI\nN° CRMV')
    tutor_pdf = d.add_task(*t, 960, 'Receber\nPDF CVI')

    end = d.add_event(*fi, 1300, 'Fim', 'end_event')

    d.add_edge(start['id'], tabela['id'])
    d.add_edge(start['id'], atend['id'])
    d.add_edge(atend['id'], gw_conv['id'])
    d.add_edge(gw_conv['id'], guia['id'])
    d.add_edge(guia['id'], lote['id'])
    d.add_edge(lote['id'], envia['id'])
    d.add_edge(envia['id'], proc['id'])
    d.add_edge(proc['id'], status['id'])

    d.add_edge(gw_conv['id'], gw_claim['id'])
    d.add_edge(gw_claim['id'], claim['id'])
    d.add_edge(claim['id'], webhook['id'])
    d.add_edge(webhook['id'], atualiza['id'])

    d.add_edge(gw_conv['id'], cvi_start['id'])
    d.add_edge(cvi_start['id'], gw_req['id'])
    d.add_edge(gw_req['id'], gera_cvi['id'])
    d.add_message(gera_cvi['id'], tutor_pdf['id'])

    d.add_edge(status['id'], end['id'])
    d.add_edge(atualiza['id'], end['id'])
    d.add_edge(gera_cvi['id'], end['id'])
    d.save()


def x4_17_lgpd():
    d = Diagram('Fluxo de Auditoria e LGPD', '22-fluxo-lgpd.svg', pw=1100, ph=500)
    d.add_pool('Tutor', [('Tutor','tutor')])
    d.add_pool('Clínica', [('Admin','admin'), ('Sistema','sistema'), ('Auditor','auditor')])

    t = 'Tutor', 'Tutor'
    a = 'Clínica', 'Admin'
    s = 'Clínica', 'Sistema'
    au = 'Clínica', 'Auditor'

    start = d.add_event(*t, 40, 'Solicitar\nDireito LGPD', 'start_event')
    gw_tipo = d.add_gateway(*s, 220, 'Tipo de\nSolicitação?', 'gateway_exclusive')
    acesso = d.add_task(*s, 420, 'Exportar\nDados (JSON)')
    correcao = d.add_task(*a, 420, 'Editar\nDados')
    exclusao = d.add_task(*s, 420, 'Anonimizar\n(lgpd:anonymize)')
    port = d.add_task(*s, 420, 'Exportar\nFormato Estruturado')
    revog = d.add_task(*a, 420, 'Revogar\nConsentimento')
    auditoria = d.add_task(*s, 650, 'Registrar\nAuditoria')
    resp = d.add_task(*t, 650, 'Resposta em\naté 15 dias')
    auditor = d.add_task(*au, 420, 'Auditar\nLogs')
    end = d.add_event(*s, 850, 'Fim', 'end_event')

    d.add_edge(start['id'], gw_tipo['id'])
    d.add_edge(gw_tipo['id'], acesso['id'])
    d.add_edge(gw_tipo['id'], correcao['id'])
    d.add_edge(gw_tipo['id'], exclusao['id'])
    d.add_edge(gw_tipo['id'], port['id'])
    d.add_edge(gw_tipo['id'], revog['id'])
    d.add_edge(acesso['id'], auditoria['id'])
    d.add_edge(correcao['id'], auditoria['id'])
    d.add_edge(exclusao['id'], auditoria['id'])
    d.add_edge(port['id'], auditoria['id'])
    d.add_edge(revog['id'], auditoria['id'])
    d.add_edge(auditoria['id'], resp['id'])
    d.add_edge(resp['id'], end['id'])
    d.add_edge(auditor['id'], auditoria['id'])
    d.save()


def x4_18_notificacao():
    d = Diagram('Fluxo de Notificações', '23-fluxo-notificacao.svg', pw=1100, ph=450)
    d.add_pool('Sistema', [('Sistema','sistema'), ('CommunicationQueue','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    s = 'Sistema', 'Sistema'
    q = 'Sistema', 'CommunicationQueue'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*s, 40, 'Evento de\nNegócio', 'start_event')
    verif = d.add_task(*s, 220, 'Verificar\nPreferências')
    gw_opt = d.add_gateway(*s, 400, 'Tutor optou\npor notifs?', 'gateway_exclusive')
    gw_canal = d.add_gateway(*q, 580, 'Hierarquia\nde Canais', 'gateway_exclusive')
    whats = d.add_task(*q, 780, 'WhatsApp\n(Z-API)')
    sms = d.add_task(*q, 780, 'SMS\n(fallback)')
    email = d.add_task(*q, 780, 'E-mail')
    log = d.add_task(*s, 960, 'Registrar\nLog de Entrega')
    gw_fail = d.add_gateway(*s, 1100, 'Falhou?', 'gateway_exclusive')
    tentar = d.add_task(*s, 1250, '3 tentativas\n+ desativar canal')
    sucesso = d.add_task(*t, 1250, 'Notificação\nRecebida')
    end = d.add_event(*s, 1400, 'Fim', 'end_event')

    d.add_edge(start['id'], verif['id'])
    d.add_edge(verif['id'], gw_opt['id'])
    d.add_edge(gw_opt['id'], gw_canal['id'])
    d.add_edge(gw_canal['id'], whats['id'])
    d.add_edge(gw_canal['id'], sms['id'])
    d.add_edge(gw_canal['id'], email['id'])
    d.add_edge(whats['id'], log['id'])
    d.add_edge(sms['id'], log['id'])
    d.add_edge(email['id'], log['id'])
    d.add_edge(log['id'], gw_fail['id'])
    d.add_edge(gw_fail['id'], tentar['id'])
    d.add_edge(gw_fail['id'], sucesso['id'])
    d.add_edge(tentar['id'], end['id'])
    d.add_edge(sucesso['id'], end['id'])
    d.save()


def x4_19_chat():
    d = Diagram('Fluxo de Chat Tutor ↔ Clínica', '24-fluxo-chat.svg', pw=900, ph=400)
    d.add_pool('Tutor (Portal)', [('Tutor','tutor')])
    d.add_pool('Clínica', [('Funcionário','veterinario'), ('Sistema','sistema')])

    t = 'Tutor (Portal)', 'Tutor'
    f = 'Clínica', 'Funcionário'
    s = 'Clínica', 'Sistema'

    start = d.add_event(*t, 40, 'Abrir Chat', 'start_event')
    gw_anexo = d.add_gateway(*t, 200, 'Anexo?', 'gateway_exclusive')
    validar = d.add_task(*t, 360, 'Validar\n(máx 10MB)')
    anexar = d.add_task(*t, 360, 'Anexar\nImagem/PDF')
    envia = d.add_task(*t, 530, 'Enviar\nMensagem')
    persiste = d.add_task(*s, 530, 'Persiste em\nchat_messages')
    badge = d.add_task(*f, 530, 'Badge de\nNão Lido')
    abre = d.add_task(*f, 730, 'Abrir\nConversa')
    responde = d.add_task(*f, 900, 'Digitar\nResposta')
    notif = d.add_event(*s, 730, 'Notificação\nPush/WhatsApp', 'intermediate_event')
    visualiza = d.add_task(*t, 900, 'Visualizar\nResposta')
    end = d.add_event(*t, 1050, 'Fim', 'end_event')

    d.add_edge(start['id'], gw_anexo['id'])
    d.add_edge(gw_anexo['id'], validar['id'])
    d.add_edge(gw_anexo['id'], envia['id'])
    d.add_edge(validar['id'], anexar['id'])
    d.add_edge(anexar['id'], envia['id'])
    d.add_edge(envia['id'], persiste['id'])
    d.add_edge(persiste['id'], badge['id'])
    d.add_edge(badge['id'], abre['id'])
    d.add_edge(abre['id'], responde['id'])
    d.add_edge(abre['id'], notif['id'])
    d.add_edge(notif['id'], visualiza['id'])
    d.add_edge(responde['id'], persiste['id'])
    d.add_edge(visualiza['id'], end['id'])
    d.save()


def x4_20_autoupdate():
    d = Diagram('Fluxo de Auto-Update e Configurações', '25-fluxo-autoupdate.svg', pw=1100, ph=400)
    d.add_pool('Admin', [('Admin','admin')])
    d.add_pool('Sistema', [('Sistema','sistema')])
    d.add_pool('GitHub', [('GitHub','github')])

    ad = 'Admin', 'Admin'
    s = 'Sistema', 'Sistema'
    gh = 'GitHub', 'GitHub'

    start = d.add_event(*ad, 40, 'Configurações\n> Atualização', 'start_event')
    config = d.add_task(*ad, 220, 'Configurar\nToken+Repo+Branch')
    verificar = d.add_task(*ad, 420, 'Verificar\nAtualizações')
    ls_remote = d.add_task(*gh, 420, 'git ls-remote')
    compare = d.add_task(*s, 420, 'Comparar\nHash Local vs Remoto')
    gw_disp = d.add_gateway(*s, 620, 'Atualização\nDisponível?', 'gateway_exclusive')
    changelog = d.add_task(*ad, 800, 'Exibir\nChangelog')
    aplicar = d.add_task(*ad, 960, 'Aplicar\nAtualização')
    atomic = d.add_task(*s, 960, 'down → git pull\n→ migrate → up')
    gw_conflict = d.add_gateway(*s, 1140, 'Merge\nConflict?', 'gateway_exclusive')
    abort = d.add_task(*s, 1300, 'Abortar\nRestaurar Backup')
    historico = d.add_task(*s, 1300, 'Registrar\nHistórico')
    end = d.add_event(*ad, 1400, 'Fim', 'end_event')

    d.add_edge(start['id'], config['id'])
    d.add_edge(config['id'], verificar['id'])
    d.add_edge(verificar['id'], ls_remote['id'])
    d.add_edge(ls_remote['id'], compare['id'])
    d.add_edge(compare['id'], gw_disp['id'])
    d.add_edge(gw_disp['id'], changelog['id'])
    d.add_edge(changelog['id'], aplicar['id'])
    d.add_edge(aplicar['id'], atomic['id'])
    d.add_edge(atomic['id'], gw_conflict['id'])
    d.add_edge(gw_conflict['id'], abort['id'])
    d.add_edge(gw_conflict['id'], historico['id'])
    d.add_edge(historico['id'], end['id'])
    d.add_edge(abort['id'], config['id'])
    d.save()


def x4_21_emergencia():
    d = Diagram('Fluxo de Emergência', '26-fluxo-emergencia.svg', pw=1000, ph=400)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*v, 40, 'Pet chega em\nEmergência', 'start_event')
    autoriza = d.add_task(*t, 220, 'Autorizar\nAtendimento')
    protocolo = d.add_task(*v, 220, 'Buscar\nProtocolo')
    filtrar = d.add_task(*v, 420, 'Filtrar por\nEspécie+Gravidade')
    seleciona = d.add_task(*v, 600, 'Selecionar\nProtocolo')
    passo = d.add_task(*v, 780, 'Seguir Passo\na Passo')
    registra = d.add_task(*v, 960, 'Registrar no\nProntuário')
    gw_int = d.add_gateway(*v, 1100, 'Internar?', 'gateway_exclusive')
    internar = d.add_task(*s, 1250, 'Fluxo\nInternação')
    notif = d.add_event(*s, 780, 'Notificar\nTutor', 'intermediate_event')
    end = d.add_event(*v, 1400, 'Fim', 'end_event')

    d.add_edge(start['id'], autoriza['id'])
    d.add_edge(autoriza['id'], protocolo['id'])
    d.add_edge(protocolo['id'], filtrar['id'])
    d.add_edge(filtrar['id'], seleciona['id'])
    d.add_edge(seleciona['id'], passo['id'])
    d.add_edge(passo['id'], registra['id'])
    d.add_edge(registra['id'], gw_int['id'])
    d.add_edge(gw_int['id'], internar['id'])
    d.add_edge(gw_int['id'], end['id'])
    d.add_edge(passo['id'], notif['id'])
    d.add_message(notif['id'], autoriza['id'])
    d.save()


def x4_22_triagem():
    d = Diagram('Fluxo de Triagem Manchester', '28-fluxo-triagem.svg', pw=1100, ph=450)
    d.add_pool('Clínica', [('Recepcionista','recepcionista'), ('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    r = 'Clínica', 'Recepcionista'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*r, 40, 'Tutor chega\ncom Pet', 'start_event')
    queixa = d.add_task(*r, 220, 'Registrar Queixa\n+ Sinais Vitais')
    classificar = d.add_task(*r, 420, 'Classificar\nManchester (cor)')
    timer = d.add_event(*s, 420, 'Temporizador\n(cor++)', 'timer')
    gw_tempo = d.add_gateway(*s, 600, 'Tempo Máx\nExcedido?', 'gateway_exclusive')
    escalar = d.add_task(*s, 760, 'Escalar\nPrioridade')
    sugestao = d.add_task(*s, 760, 'Sugerir\nProtocolo')
    atender = d.add_task(*v, 760, 'Realizar\nAtendimento')
    registrar = d.add_task(*v, 960, 'Registrar na\nTimeline do Pet')
    end = d.add_event(*r, 1100, 'Fim', 'end_event')

    d.add_edge(start['id'], queixa['id'])
    d.add_edge(queixa['id'], classificar['id'])
    d.add_edge(classificar['id'], timer['id'])
    d.add_edge(timer['id'], gw_tempo['id'])
    d.add_edge(gw_tempo['id'], escalar['id'])
    d.add_edge(gw_tempo['id'], atender['id'])
    d.add_edge(escalar['id'], sugestao['id'])
    d.add_edge(sugestao['id'], atender['id'])
    d.add_edge(atender['id'], registrar['id'])
    d.add_edge(registrar['id'], end['id'])
    d.save()


def x4_23_hospedagem():
    d = Diagram('Fluxo de Hospedagem/Boarding', '29-fluxo-hospedagem.svg', pw=1200, ph=500)
    d.add_pool('Clínica', [('Recepcionista','recepcionista'), ('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    r = 'Clínica', 'Recepcionista'
    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*r, 40, 'Solicitar\nHospedagem', 'start_event')
    checkin = d.add_task(*r, 220, 'Check-in\n+ Vacinas OK?')
    assina = d.add_task(*t, 220, 'Assinar Termo\nde Responsabilidade')
    aloca = d.add_task(*r, 420, 'Alocar\nAcomodação')
    gw_bh = d.add_gateway(*r, 600, 'Banho e\nTosa?', 'gateway_exclusive')
    grooming = d.add_task(*r, 760, 'Agendar\nGrooming')
    ciclo = d.add_subprocess(*s, 760, 'Ciclo Diário:\nAlim+Med+Pas+Limpeza')
    inter = d.add_event(*s, 960, 'Intercorrência?\n→ Notificar Vet', 'intermediate_event')
    notif_tutor = d.add_task(*t, 960, 'Notificado')
    gw_co = d.add_gateway(*r, 1100, 'Check-out?', 'gateway_exclusive')
    diarias = d.add_task(*r, 1250, 'Calcular\nDiárias')
    fatura = d.add_task(*r, 1400, 'Gerar\nFatura')
    paga = d.add_task(*t, 1400, 'Pagar e\nRetirar Pet')
    end = d.add_event(*r, 1500, 'Fim', 'end_event')

    d.add_edge(start['id'], checkin['id'])
    d.add_edge(checkin['id'], assina['id'])
    d.add_edge(assina['id'], aloca['id'])
    d.add_edge(aloca['id'], gw_bh['id'])
    d.add_edge(gw_bh['id'], grooming['id'])
    d.add_edge(gw_bh['id'], ciclo['id'])
    d.add_edge(grooming['id'], ciclo['id'])
    d.add_edge(ciclo['id'], inter['id'])
    d.add_message(inter['id'], notif_tutor['id'])
    d.add_edge(inter['id'], gw_co['id'])
    d.add_edge(ciclo['id'], gw_co['id'])
    d.add_edge(gw_co['id'], diarias['id'])
    d.add_edge(gw_co['id'], ciclo['id'])  # loop back
    d.add_edge(diarias['id'], fatura['id'])
    d.add_edge(fatura['id'], paga['id'])
    d.add_edge(paga['id'], end['id'])
    d.save()


def x4_24_odontologia():
    d = Diagram('Fluxo de Odontologia', '30-fluxo-odontologia.svg', pw=1100, ph=500)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])

    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'

    start = d.add_event(*v, 40, 'Abrir Ficha\nOdontológica', 'start_event')
    odonto = d.add_task(*s, 220, 'Exibir\nOdontograma')
    clica = d.add_task(*v, 220, 'Clicar no\nDente')
    gw_proc = d.add_gateway(*v, 420, 'Procedimento\nNecessário?', 'gateway_exclusive')
    paral = d.add_gateway(*v, 580, '', 'gateway_parallel')
    limpeza = d.add_task(*v, 760, 'Profilaxia\n(Raspagem)')
    extracao = d.add_task(*v, 760, 'Extração\n(Raio-X OBRIG)')
    rest = d.add_task(*v, 760, 'Restauração\n/ Canal')
    gengiv = d.add_task(*v, 760, 'Gengivectomia')
    autoriza = d.add_task(*t, 760, 'Autorizar\nProcedimento')
    realiza = d.add_task(*v, 960, 'Realizar\nProcedimento')
    presc = d.add_task(*v, 1100, 'Prescrição\nPós-OP')
    retorno = d.add_task(*v, 1250, 'Agendar\nRetorno')
    timeline = d.add_task(*s, 1250, 'Registrar\nna Timeline')
    end = d.add_event(*v, 1400, 'Fim', 'end_event')

    d.add_edge(start['id'], odonto['id'])
    d.add_edge(odonto['id'], clica['id'])
    d.add_edge(clica['id'], gw_proc['id'])
    d.add_edge(gw_proc['id'], paral['id'])
    d.add_edge(paral['id'], limpeza['id'])
    d.add_edge(paral['id'], extracao['id'])
    d.add_edge(paral['id'], rest['id'])
    d.add_edge(paral['id'], gengiv['id'])
    d.add_edge(limpeza['id'], autoriza['id'])
    d.add_edge(extracao['id'], autoriza['id'])
    d.add_edge(rest['id'], autoriza['id'])
    d.add_edge(gengiv['id'], autoriza['id'])
    d.add_edge(autoriza['id'], realiza['id'])
    d.add_edge(realiza['id'], presc['id'])
    d.add_edge(presc['id'], retorno['id'])
    d.add_edge(retorno['id'], timeline['id'])
    d.add_edge(timeline['id'], end['id'])
    d.save()


def x4_25_zoonoses():
    d = Diagram('Fluxo de Zoonoses e Notificação Compulsória', '31-fluxo-zoonoses.svg', pw=1100, ph=500)
    d.add_pool('Clínica', [('Veterinário','veterinario'), ('Sistema','sistema')])
    d.add_pool('Tutor', [('Tutor','tutor')])
    d.add_pool('Vigilância Sanitária', [('Vigilância','vigilancia')])

    v = 'Clínica', 'Veterinário'
    s = 'Clínica', 'Sistema'
    t = 'Tutor', 'Tutor'
    vig = 'Vigilância Sanitária', 'Vigilância'

    start = d.add_event(*v, 40, 'Diagnosticar\nZoonose', 'start_event')
    gw_comp = d.add_gateway(*s, 220, 'Notificação\nCompulsória?', 'gateway_exclusive')
    prazo = d.add_task(*s, 420, 'Prazos: Raiva\n24h / Leptospirose 24h')
    formulario = d.add_task(*v, 420, 'Gerar Formulário\nOficial')
    enviar = d.add_task(*v, 620, 'Enviar ao\nÓrgão Competente')
    recebe = d.add_task(*vig, 620, 'Receber\nNotificação')
    protocolo = d.add_task(*s, 620, 'Registrar\nProtocolo')
    diagnostico = d.add_task(*t, 420, 'Informar\nDiagnóstico')
    controle = d.add_task(*v, 820, 'Medidas de\nControle')
    encaminha = d.add_task(*t, 820, 'Encaminhamento\nMédico')
    relatorio = d.add_task(*s, 820, 'Relatório\nEpidemiológico')
    end = d.add_event(*v, 1020, 'Fim', 'end_event')

    d.add_edge(start['id'], gw_comp['id'])
    d.add_edge(gw_comp['id'], prazo['id'])
    d.add_edge(gw_comp['id'], end['id'])
    d.add_edge(prazo['id'], formulario['id'])
    d.add_edge(formulario['id'], enviar['id'])
    d.add_edge(enviar['id'], recebe['id'])
    d.add_edge(enviar['id'], protocolo['id'])
    d.add_edge(protocolo['id'], diagnostico['id'])
    d.add_edge(diagnostico['id'], controle['id'])
    d.add_edge(diagnostico['id'], encaminha['id'])
    d.add_edge(controle['id'], relatorio['id'])
    d.add_edge(relatorio['id'], end['id'])
    d.save()


def x4_26_rh():
    d = Diagram('Fluxo de Recursos Humanos (Admissão)', 'rh-fluxo-admissao.svg', pw=1000, ph=400)
    d.add_pool('Clínica', [('RH','rh'), ('Admin','admin'), ('Sistema','sistema')])

    rh = 'Clínica', 'RH'
    a = 'Clínica', 'Admin'
    s = 'Clínica', 'Sistema'

    start = d.add_event(*rh, 40, 'Iniciar\nAdmissão', 'start_event')
    dept = d.add_task(*rh, 200, 'Criar\nDepartamento')
    cargo = d.add_task(*rh, 200, 'Criar\nCargo/Posição')
    func = d.add_task(*rh, 420, 'Cadastrar\nFuncionário')
    gw_acesso = d.add_gateway(*rh, 620, 'Acesso ao\nSistema?', 'gateway_exclusive')
    vinculo = d.add_task(*s, 780, 'Vincular\nUsuário')
    role = d.add_task(*a, 780, 'Atribuir\nRole+Permissões')
    escala = d.add_task(*s, 960, 'Definir\nEscala')
    gw_plantao = d.add_gateway(*rh, 960, 'Plantão?', 'gateway_exclusive')
    plantao = d.add_task(*rh, 1120, 'Configurar\nSobreaviso')
    lembrete = d.add_task(*s, 1120, 'Lembretes\n(staff:remind)')
    end = d.add_event(*rh, 1280, 'Fim', 'end_event')

    d.add_edge(start['id'], dept['id'])
    d.add_edge(start['id'], cargo['id'])
    d.add_edge(dept['id'], func['id'])
    d.add_edge(cargo['id'], func['id'])
    d.add_edge(func['id'], gw_acesso['id'])
    d.add_edge(gw_acesso['id'], vinculo['id'])
    d.add_edge(gw_acesso['id'], escala['id'])
    d.add_edge(vinculo['id'], role['id'])
    d.add_edge(role['id'], escala['id'])
    d.add_edge(escala['id'], gw_plantao['id'])
    d.add_edge(gw_plantao['id'], plantao['id'])
    d.add_edge(gw_plantao['id'], lembrete['id'])
    d.add_edge(plantao['id'], lembrete['id'])
    d.add_edge(lembrete['id'], end['id'])
    d.save()


def x4_27_relatorio():
    d = Diagram('Fluxo de Relatórios', '21-fluxo-relatorio.svg', pw=900, ph=400)
    d.add_pool('Clínica', [('Usuário','sistema'), ('Sistema','sistema')])

    u = 'Clínica', 'Usuário'
    s = 'Clínica', 'Sistema'

    start = d.add_event(*u, 40, 'Acessar\nMódulo Relatórios', 'start_event')
    tipo = d.add_task(*u, 220, 'Selecionar\nTipo+Filtros')
    gw_formato = d.add_gateway(*u, 420, 'Formato?', 'gateway_exclusive')
    tela = d.add_task(*u, 580, 'Visualizar\nna Tela')
    pdf = d.add_task(*s, 580, 'Gerar PDF')
    excel = d.add_task(*s, 580, 'Exportar\nExcel/CSV')
    gw_agendar = d.add_gateway(*u, 760, 'Agendar\nEnvio?', 'gateway_exclusive')
    recorrencia = d.add_task(*s, 920, 'Configurar\nRecorrência')
    enviar = d.add_task(*s, 920, 'Enviar por\nE-mail')
    end = d.add_event(*u, 1080, 'Fim', 'end_event')

    d.add_edge(start['id'], tipo['id'])
    d.add_edge(tipo['id'], gw_formato['id'])
    d.add_edge(gw_formato['id'], tela['id'])
    d.add_edge(gw_formato['id'], pdf['id'])
    d.add_edge(gw_formato['id'], excel['id'])
    d.add_edge(tela['id'], gw_agendar['id'])
    d.add_edge(pdf['id'], gw_agendar['id'])
    d.add_edge(excel['id'], gw_agendar['id'])
    d.add_edge(gw_agendar['id'], recorrencia['id'])
    d.add_edge(gw_agendar['id'], end['id'])
    d.add_edge(recorrencia['id'], enviar['id'])
    d.add_edge(enviar['id'], end['id'])
    d.save()


def x4_28_mobile():
    d = Diagram('Fluxo Mobile (Veterinário em Campo)', '27-fluxo-mobile.svg', pw=900, ph=400)
    d.add_pool('Mobile', [('Veterinário','veterinario'), ('Sistema','sistema')])

    v = 'Mobile', 'Veterinário'
    s = 'Mobile', 'Sistema'

    start = d.add_event(*v, 40, 'Acessar /m\nno celular', 'start_event')
    gw_acao = d.add_gateway(*v, 220, 'Ação\nDesejada?', 'gateway_exclusive')
    agenda = d.add_task(*v, 420, 'Agenda\ndo Dia')
    pesquisa = d.add_task(*v, 420, 'Pesquisar\nPet')
    checkin = d.add_task(*v, 420, 'Check-in\n(confirmação)')
    vacina = d.add_task(*v, 420, 'Vacinação\nRápida')
    scanner = d.add_task(*v, 420, 'Scanner\n(Código/QR)')
    chat = d.add_task(*v, 420, 'Chat com\nTutor')
    triagem = d.add_task(*v, 420, 'Visualizar\nTriagem')
    end = d.add_event(*v, 700, 'Fim', 'end_event')

    d.add_edge(start['id'], gw_acao['id'])
    d.add_edge(gw_acao['id'], agenda['id'])
    d.add_edge(gw_acao['id'], pesquisa['id'])
    d.add_edge(gw_acao['id'], checkin['id'])
    d.add_edge(gw_acao['id'], vacina['id'])
    d.add_edge(gw_acao['id'], scanner['id'])
    d.add_edge(gw_acao['id'], chat['id'])
    d.add_edge(gw_acao['id'], triagem['id'])
    d.add_edge(agenda['id'], end['id'])
    d.add_edge(pesquisa['id'], end['id'])
    d.add_edge(checkin['id'], end['id'])
    d.add_edge(vacina['id'], end['id'])
    d.add_edge(scanner['id'], end['id'])
    d.add_edge(chat['id'], end['id'])
    d.add_edge(triagem['id'], end['id'])
    d.save()


# ══════════════════════════════════════════════════════════════
#  MAIN
# ══════════════════════════════════════════════════════════════

if __name__ == '__main__':
    os.makedirs(OUTDIR, exist_ok=True)
    print('Gerando diagramas BPMN 2.0 (Phase X)...\n')

    x4_1_macro_fluxo()
    x4_2_matriz_perfis()
    x4_3_prontuario()
    x4_4_prescricao()
    x4_5_vacina()
    x4_6_exame()
    x4_7_laboratorio()
    x4_8_imagem()
    x4_9_cirurgia()
    x4_10_internacao()
    x4_11_farmacia()
    x4_12_estoque()
    x4_12b_substancias()
    x4_13_fatura()
    x4_13b_conciliacao()
    x4_14_agendamento()
    x4_15_tutor_pet()
    x4_16_convenio()
    x4_17_lgpd()
    x4_18_notificacao()
    x4_19_chat()
    x4_20_autoupdate()
    x4_21_emergencia()
    x4_22_triagem()
    x4_23_hospedagem()
    x4_24_odontologia()
    x4_25_zoonoses()
    x4_26_rh()
    x4_27_relatorio()
    x4_28_mobile()

    print(f'\nTotal: 31 diagramas gerados em {OUTDIR}/')
