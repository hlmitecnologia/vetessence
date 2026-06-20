#!/usr/bin/env python3
"""
Convert BPMN SVG (with embedded mxGraph XML) to PlantUML Activity Diagram.
Parses swimlanes, activities, decisions, and edges to reconstruct the flow.
"""
import re
import sys
import os

LANE_COLORS = {
    'Tutor': '#E1D5E7',
    'Recepcionista': '#DAE8FC',
    'Veterinário': '#D5E8D4',
    'Financeiro': '#FFE6CC',
    'Estoque': '#FFF2CC',
    'Sistema': '#F5F5F5',
    'Admin': '#D5E8D4',
    'Enfermeiro': '#D5E8D4',
    'Fornecedor': '#DAE8FC',
    'Banco': '#FFF2CC',
    'Webmania': '#FFF2CC',
    'Público': '#E1D5E7',
    'RH': '#D5E8D4',
    'Usuário': '#DAE8FC',
    'Funcionário': '#DAE8FC',
    '': '#F5F5F5',
}

SWIMLANE_Y_RANGES = {}  # Will be populated

def parse_swimlanes(mx_cells):
    """Extract swimlane name → (y_start, y_end) from mxGraph cells."""
    lanes = {}
    # First pass: get lane containers and their positions
    containers = {}  # id → {name, y, height}
    
    for cell in mx_cells:
        cid = cell.get('id', '')
        style = cell.get('style', '')
        value = cell.get('value', '').strip().replace('\n', ' ').replace('\r', '')
        geo = cell.get('geo', {})
        parent = cell.get('parent', '')
        
        if 'swimlane' in style and value:
            y = float(geo.get('y', 0))
            h = float(geo.get('height', 80))
            # Check if this is a lane (startSize > 0) or pool (startSize = 0)
            if 'startSize=0' in style or 'startSize="0"' in style:
                # This is a pool container - track it
                containers[cid] = {'name': value, 'y': y, 'height': h, 'sub_y': None, 'sub_h': None}
            else:
                # This is an actual swimlane
                # Find parent pool
                parent_name = containers.get(parent, {}).get('name', '')
                lanes[cid] = {'name': value, 'y': y, 'height': h, 'pool': parent_name}
    
    return lanes, containers

def get_lane_for_y(y, lanes, containers):
    """Determine which lane an activity belongs to based on its Y position."""
    best_lane = None
    for cid, lane in lanes.items():
        ly = lane['y']
        lh = lane['height']
        if ly <= y < ly + lh:
            return lane['name']
        # Also check if it's close (within the lane bounds)
        if ly - 10 <= y < ly + lh + 10:
            return lane['name']
    # Fallback: try from containers
    for cid, cont in containers.items():
        cy = cont['y']
        ch = cont['height']
        if cy <= y < cy + ch:
            # Check if sub-lanes exist within this container
            for lcid, lane in lanes.items():
                if lane['pool'] == cont['name']:
                    if lane['y'] <= y < lane['y'] + lane['height']:
                        return lane['name']
            return cont['name']
    return 'Sistema'

def normalize_value(val):
    """Clean up value text."""
    val = val.replace('\n', ' ').replace('\r', '').strip()
    val = re.sub(r'\s+', ' ', val)
    # Truncate long values
    if len(val) > 40:
        val = val[:38] + '...'
    return val

def is_decision(cell_text, style):
    """Check if cell is a decision diamond."""
    return 'rhombus' in style or cell_text.endswith('?')

def is_end_event(cell_text, style):
    """Check if cell is an end event."""
    return ('ellipse' in style and 
            cell_text.lower() not in ['início', 'start', '', 'pending', 'confirmed'])

def is_start_event(cell_text, style):
    """Check if cell is a start event."""
    return ('ellipse' in style and 
            cell_text.lower() in ['início', 'start', 'selecionar pet', ''])

def extract_flow(svg_path):
    """Extract flow data from SVG with embedded mxGraph."""
    with open(svg_path) as f:
        content = f.read()
    
    # Extract foreignObject
    fo_match = re.search(r'<foreignObject[^>]*>(.*?)</foreignObject>', content, re.DOTALL)
    if not fo_match:
        print(f"No mxGraph data in {svg_path}")
        return None
    
    raw = fo_match.group(1)
    
    # Parse all mxCell elements
    cells = re.findall(r'<mxCell[^>]*>.*?</mxCell>', raw, re.DOTALL) + re.findall(r'<mxCell[^>]*/>', raw)
    
    parsed_cells = []
    for cell_str in cells:
        cell = {}
        cell['id'] = re.search(r'id="([^"]*)"', cell_str)
        cell['parent'] = re.search(r'parent="([^"]*)"', cell_str)
        cell['value'] = re.search(r'value="([^"]*)"', cell_str)
        cell['style'] = re.search(r'style="([^"]*)"', cell_str)
        cell['source'] = re.search(r'source="([^"]*)"', cell_str)
        cell['target'] = re.search(r'target="([^"]*)"', cell_str)
        cell['edge'] = 'edge="1"' in cell_str or 'edge="1"' in cell_str
        cell['vertex'] = 'vertex="1"' in cell_str or 'vertex="1"' in cell_str
        
        # Geometry
        geo = re.search(r'<mxGeometry[^>]*/>', cell_str)
        if not geo:
            geo = re.search(r'<mxGeometry[^>]*>', cell_str)
        if geo:
            gs = geo.group(0)
            cell['geo'] = {
                'x': re.search(r'x="([^"]*)"', gs),
                'y': re.search(r'y="([^"]*)"', gs),
                'width': re.search(r'width="([^"]*)"', gs),
                'height': re.search(r'height="([^"]*)"', gs),
            }
            cell['geo'] = {k: (m.group(1) if m else '0') for k, m in cell['geo'].items()}
        else:
            cell['geo'] = {'x': '0', 'y': '0', 'width': '0', 'height': '0'}
        
        for k in ['id', 'parent', 'value', 'style', 'source', 'target']:
            if cell[k]:
                cell[k] = cell[k].group(1)
            else:
                cell[k] = ''
        
        parsed_cells.append(cell)
    
    # Extract title
    title = ''
    for c in parsed_cells:
        if c['value'] and ('Fluxo' in c['value'] or 'Macro' in c['value'] or 'Recursos Humanos' in c['value']):
            title = c['value'].replace('\n', ' ').strip()
            break
    
    # Extract swimlanes
    lanes = {}
    containers = {}
    for c in parsed_cells:
        style = c['style']
        value = c['value'].strip()
        geo = c['geo']
        if 'swimlane' in style and value and c['parent'] != '0':
            y = float(geo.get('y', 0))
            h = float(geo.get('height', 80))
            if 'startSize=0' in style or 'startSize="0"' in style:
                containers[c['id']] = {'name': value, 'y': y, 'height': h}
            else:
                parent_name = containers.get(c['parent'], {}).get('name', '')
                lanes[c['id']] = {'name': value, 'y': y, 'height': h, 'pool': parent_name}
    
    # Extract edges and activities
    edges = []
    activities = {}  # id → {name, y, style, is_decision}
    
    # Title values to skip
    title_vals = {title} if title else set()
    
    for c in parsed_cells:
        if c['edge'] and c['source'] and c['target']:
            edges.append({
                'source': c['source'],
                'target': c['target'],
                'dashed': 'dashed' in c['style'],
            })
        elif c['vertex'] and c['value'] and not c['edge']:
            v = c['value'].replace('\n', ' ').strip()
            if v and 'swimlane' not in c['style'] and v not in title_vals:
                is_dec = 'rhombus' in c['style'] or v.endswith('?')
                y = float(c['geo'].get('y', 0))
                activities[c['id']] = {
                    'name': v,
                    'y': y,
                    'style': c['style'],
                    'is_decision': is_dec,
                }
    
    return {
        'title': title,
        'lanes': lanes,
        'containers': containers,
        'edges': edges,
        'activities': activities,
    }

def generate_plantuml(flow, out_path):
    """Generate PlantUML file from extracted flow."""
    if not flow:
        return
    
    title = flow['title']
    lanes = flow['lanes']
    containers = flow['containers']
    edges = flow['edges']
    activities = flow['activities']
    
    # Get unique lane names in order of appearance (by Y position)
    lane_order = sorted(lanes.values(), key=lambda l: l['y'])
    lane_names = []
    seen = set()
    for l in lane_order:
        n = l['name']
        if n not in seen:
            seen.add(n)
            lane_names.append(n)
    
    # If no lanes found, try containers as lanes
    if not lane_names:
        cont_order = sorted(containers.values(), key=lambda c: c['y'])
        for c in cont_order:
            lane_names.append(c['name'])
    
    if not lane_names:
        lane_names = ['Sistema']
    
    # Build flow graph from edges
    # For each activity, find its outgoing edges
    outgoing = {id: [] for id in activities}
    for e in edges:
        if e['source'] in outgoing:
            outgoing[e['source']].append(e)
    
    # Find start nodes (no incoming edges)
    all_targets = set(e['target'] for e in edges)
    start_nodes = [id for id in activities if id not in all_targets]
    
    # Write PlantUML
    with open(out_path, 'w') as f:
        f.write('@startuml\n')
        f.write('!include _common.puml\n\n')
        if title:
            f.write(f'title {title}\n\n')
        
        # Track current lane for |swimlane| transitions
        current_lane = ''
        
        def write_activity(aid, indent=0):
            nonlocal current_lane
            if aid not in activities:
                return
            act = activities[aid]
            
            # Determine lane
            act_lane = get_lane_for_y(act['y'], lanes, containers)
            
            # If lane changed, output lane transition
            if act_lane and act_lane != current_lane:
                f.write(f'{" " * indent}|{act_lane}|\n')
                current_lane = act_lane
            
            name = normalize_value(act['name'])
            
            if act['is_decision']:
                outs = outgoing.get(aid, [])
                # Separate dashed (message/no) from solid (flow/yes) edges
                no_targets = [e['target'] for e in outs if e['dashed']]
                yes_targets = [e['target'] for e in outs if not e['dashed']]
                
                if len(yes_targets) == 1 and len(no_targets) == 1:
                    f.write(f'{" " * indent}if ({name}) then ([não])\n')
                    write_activity(no_targets[0], indent + 2)
                    f.write(f'{" " * indent}else ([sim])\n')
                    write_activity(yes_targets[0], indent + 2)
                    f.write(f'{" " * indent}endif\n')
                elif len(yes_targets) >= 1:
                    # Single yes-target, no explicit no-target
                    f.write(f'{" " * indent}if ({name}) then ([sim])\n')
                    write_activity(yes_targets[0], indent + 2)
                    f.write(f'{" " * indent}endif\n')
                elif len(no_targets) >= 1:
                    f.write(f'{" " * indent}if ({name}) then ([não])\n')
                    write_activity(no_targets[0], indent + 2)
                    f.write(f'{" " * indent}endif\n' if not yes_targets else f'{" " * indent}else ([sim])\n{" " * indent}endif\n')
                elif len(outs) == 2:
                    # No dashed/solid distinction - use first as não, second as sim
                    f.write(f'{" " * indent}if ({name}) then ([não])\n')
                    write_activity(outs[0]['target'], indent + 2)
                    f.write(f'{" " * indent}else ([sim])\n')
                    write_activity(outs[1]['target'], indent + 2)
                    f.write(f'{" " * indent}endif\n')
                elif len(outs) == 1:
                    f.write(f'{" " * indent}if ({name}) then ([sim])\n')
                    write_activity(outs[0]['target'], indent + 2)
                    f.write(f'{" " * indent}endif\n')
            else:
                f.write(f'{" " * indent}:{name};\n')
                # Follow single outgoing edge (if not end event)
                outs = outgoing.get(aid, [])
                if len(outs) == 1:
                    write_activity(outs[0]['target'], indent)
                elif len(outs) > 1:
                    # Parallel flow - use split
                    f.write(f'{" " * indent}split\n')
                    for i, e in enumerate(outs):
                        if i > 0:
                            f.write(f'{" " * indent}split again\n')
                        write_activity(e['target'], indent + 2)
                    f.write(f'{" " * indent}endsplit\n')
        
        # Process each start node
        if len(start_nodes) == 0:
            # No clear start - use first activity by Y position
            sorted_acts = sorted(activities.items(), key=lambda a: a[1]['y'])
            if sorted_acts:
                first_id = sorted_acts[0][0]
                f.write('start\n')
                write_activity(first_id, 0)
        elif len(start_nodes) == 1:
            f.write('start\n')
            write_activity(start_nodes[0], 0)
        else:
            f.write('start\n')
            # Multiple starts - run them in parallel
            f.write('split\n')
            for i, sid in enumerate(start_nodes):
                if i > 0:
                    f.write('split again\n')
                write_activity(sid, 2)
            f.write('endsplit\n')
        
        f.write('\nstop\n@enduml\n')

    print(f'  Generated: {out_path}')

def main():
    svg_path = sys.argv[1]
    out_path = sys.argv[2] if len(sys.argv) > 2 else svg_path.replace('.svg', '.puml')
    
    print(f'Processing: {svg_path}')
    flow = extract_flow(svg_path)
    if flow:
        generate_plantuml(flow, out_path)
        return 0
    return 1

if __name__ == '__main__':
    sys.exit(main())
