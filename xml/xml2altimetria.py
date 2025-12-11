import xml.etree.ElementTree as ET

NS = {'u': 'http://www.uniovi.es'}

# Leer XML y extraer distancias y altitudes
tree = ET.parse('circuitoEsquema.xml')
root = tree.getroot()

puntos = []
dist_total = 0

# Punto de origen
alt0 = root.findtext('.//u:puntoOrigen/u:altitud', namespaces=NS)
if alt0:
    puntos.append((0, float(alt0)))

# Tramos
for tramo in root.findall('.//u:tramos/u:tramo', NS):
    d = float(tramo.get('distancia'))
    dist_total += d
    alt = tramo.findtext('u:coordenadas/u:altitud', namespaces=NS)
    if alt:
        puntos.append((dist_total, float(alt)))

# Escalar a SVG (simple)
ancho, alto = 800, 300
margen = 50
x_max = dist_total
y_max = max(a for _, a in puntos)
y_min = min(a for _, a in puntos)

def escalar_x(x): return margen + (x / x_max) * (ancho - 2*margen)
def escalar_y(y): return alto - margen - ((y - y_min) / (y_max - y_min)) * (alto - 2*margen)

# Generar polyline
polyline = " ".join(f"{escalar_x(x):.1f},{escalar_y(y):.1f}" for x, y in puntos)

# Crear SVG
with open('altimetria.svg', 'w', encoding='utf-8') as f:
    f.write(f'<svg xmlns="http://www.w3.org/2000/svg" width="{ancho}" height="{alto}">\n')
    f.write('<rect width="100%" height="100%" fill="white"/>\n')
    f.write(f'<polyline points="{polyline}" fill="none" stroke="red" stroke-width="2"/>\n')
    f.write('</svg>\n')

print("Archivo altimetria.svg generado.")