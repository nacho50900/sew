import xml.etree.ElementTree as ET

# Namespace del XML
NS = {'u': 'http://www.uniovi.es'}

def dms_a_decimal(txt):
    """Convierte '101.44.16' (grados.minutos.segundos) a decimal."""
    if not txt:
        return None
    s = txt.strip().replace(':', '.')
    partes = s.split('.')
    try:
        if len(partes) == 3:
            g, m, sec = float(partes[0]), float(partes[1]), float(partes[2])
        elif len(partes) == 2:
            g, m, sec = float(partes[0]), float(partes[1]), 0.0
        else:
            return float(s)
        signo = -1 if g < 0 else 1
        g = abs(g)
        return signo * (g + m/60 + sec/3600)
    except ValueError:
        return None

# Leer el XML
tree = ET.parse('circuitoEsquema.xml')
root = tree.getroot()

coords = []

# Punto de origen
p0 = root.find('.//u:puntoOrigen', NS)
if p0 is not None:
    lon_txt = p0.findtext('u:longitud', namespaces=NS)
    lat_txt = p0.findtext('u:latitud', namespaces=NS)
    lon = dms_a_decimal(lon_txt)
    lat = dms_a_decimal(lat_txt)
    if lon and lat:
        coords.append((lon, lat))

# Tramos
for c in root.findall('.//u:tramos/u:tramo/u:coordenadas', NS):
    lon_txt = c.findtext('u:longitud', namespaces=NS)
    lat_txt = c.findtext('u:latitud', namespaces=NS)
    lon = dms_a_decimal(lon_txt)
    lat = dms_a_decimal(lat_txt)
    if lon and lat:
        coords.append((lon, lat))

print(f"Puntos leídos: {len(coords)}")

# Generar KML
with open('circuito.kml', 'w', encoding='utf-8') as f:
    f.write('<?xml version="1.0" encoding="UTF-8"?>\n')
    f.write('<kml xmlns="http://www.opengis.net/kml/2.2">\n')
    f.write('  <Document>\n')
    f.write('    <Placemark>\n')
    f.write('      <name>Circuito</name>\n')
    f.write('      <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle></Style>\n')
    f.write('      <LineString>\n')
    f.write('        <tessellate>1</tessellate>\n')
    f.write('        <coordinates>\n')
    for lon, lat in coords:
        f.write(f'          {lon:.7f},{lat:.7f}\n')
    f.write('        </coordinates>\n')
    f.write('      </LineString>\n')
    f.write('    </Placemark>\n')
    f.write('  </Document>\n')
    f.write('</kml>\n')

print("Archivo 'circuito.kml' generado.")