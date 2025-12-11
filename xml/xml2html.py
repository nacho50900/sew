# xml2html.py
# Genera InfoCircuito.html desde circuitoEsquema.xml (sin puntoOrigen ni tramos)

import xml.etree.ElementTree as ET

# Clase muy simple para construir HTML
class Html:
    def __init__(self):
        self.partes = []
    def add(self, s):
        self.partes.append(s)
    def render(self):
        return "\n".join(self.partes)

# Namespace del XML (importante para XPath)
NS = {'u': 'http://www.uniovi.es'}

# Leer el XML
tree = ET.parse('circuitoEsquema.xml')
root = tree.getroot()

# Helpers de lectura (siempre con XPath)
def txt(path, default=""):
    t = root.findtext(path, namespaces=NS)
    return t if t is not None else default

def attr(elem_path, nombre_attr, default=""):
    el = root.find(elem_path, namespaces=NS)
    if el is not None:
        v = el.get(nombre_attr)
        return v if v else default
    return default

# === Datos básicos (SIN puntoOrigen ni tramos) ===
nombre       = txt('.//u:nombre', '—')
long_total   = txt('.//u:longitudTotal', '—')
long_ud      = attr('.//u:longitudTotal', 'unidad', 'metros')
anchura      = txt('.//u:anchura', '—')
anch_ud      = attr('.//u:anchura', 'unidad', 'metros')
fecha        = txt('.//u:fecha', '—')
hora         = txt('.//u:hora', '—')
vueltas      = txt('.//u:vueltas', '—')
localidad    = txt('.//u:localidad', '—')
pais         = txt('.//u:pais', '—')
patrocinador = txt('.//u:patrocinador', '—')

# Listas
referencias = [r.text for r in root.findall('.//u:referencias/u:referencia', NS) if r.text]
fotos       = [f.text for f in root.findall('.//u:galeriaFotos/u:foto',   NS) if f.text]
videos      = [v.text for v in root.findall('.//u:galeriaVideos/u:video', NS) if v.text]

# Resultados
vencedor_nombre = txt('.//u:vencedor/u:nombre', 'pendiente...')
vencedor_tiempo = txt('.//u:vencedor/u:tiempo', '00:00:00')
clasificacion   = [p.text if (p is not None and p.text) else 'pendiente...'
                   for p in root.findall('.//u:clasificacion/u:piloto', NS)]

# === Construcción del HTML ===
h = Html()
h.add('<!DOCTYPE html>')
h.add('<html lang="es">')
h.add('<head>')
h.add('  <meta charset="UTF-8">')
h.add('  <meta name="viewport" content="width=device-width, initial-scale=1.0">')
h.add('  <title>Info Circuito</title>')
h.add('  estilo.css')
h.add('</head>')
h.add('<body>')
h.add('  <header>')
h.add('    <h1>Información del Circuito</h1>')
h.add('  </header>')

h.add('  <main>')

# Datos básicos
h.add('    <section>')
h.add('      <h2>Datos básicos</h2>')
h.add(f'      <p><strong>Nombre:</strong> {nombre}</p>')
h.add(f'      <p><strong>Longitud total:</strong> {long_total} {long_ud}</p>')
h.add(f'      <p><strong>Anchura:</strong> {anchura} {anch_ud}</p>')
h.add(f'      <p><strong>Fecha:</strong> {fecha}</p>')
h.add(f'      <p><strong>Hora:</strong> {hora}</p>')
h.add(f'      <p><strong>Vueltas:</strong> {vueltas}</p>')
h.add(f'      <p><strong>Localidad:</strong> {localidad}</p>')
h.add(f'      <p><strong>País:</strong> {pais}</p>')
h.add(f'      <p><strong>Patrocinador:</strong> {patrocinador}</p>')
h.add('    </section>')

# Referencias
if referencias:
    h.add('    <section>')
    h.add('      <h2>Referencias</h2>')
    h.add('      <ul>')
    for r in referencias:
        h.add(f'        <li>{r}</li>')
    h.add('      </ul>')
    h.add('    </section>')

# Galería de fotos
h.add('    <section>')
h.add('      <h2>Galería de fotos</h2>')
if fotos:
    h.add('      <div class="galeria">')
    for src in fotos:
        h.add(f'        {src}')
    h.add('      </div>')
else:
    h.add('      <p>Sin fotos.</p>')
h.add('    </section>')

# Galería de vídeos (solo listado de rutas/archivos)
h.add('    <section>')
h.add('      <h2>Galería de vídeos</h2>')
if videos:
    h.add('      <ul>')
    for v in videos:
        h.add(f'        <li>{v}</li>')
    h.add('      </ul>')
else:
    h.add('      <p>Sin vídeos.</p>')
h.add('    </section>')

# Resultados (vencedor + clasificación)
h.add('    <section>')
h.add('      <h2>Resultados</h2>')
h.add('      <article>')
h.add('        <h3>Vencedor</h3>')
h.add(f'        <p><strong>Nombre:</strong> {vencedor_nombre}</p>')
h.add(f'        <p><strong>Tiempo:</strong> {vencedor_tiempo}</p>')
h.add('      </article>')

h.add('      <article>')
h.add('        <h3>Clasificación</h3>')
if clasificacion:
    h.add('        <ol>')
    for p in clasificacion:
        h.add(f'          <li>{p}</li>')
    h.add('        </ol>')
else:
    h.add('        <p>Sin datos de clasificación.</p>')
h.add('      </article>')
h.add('    </section>')

h.add('  </main>')

h.add('  <footer>')
h.add('    <p>InfoCircuito · MotoGP Desktop</p>')
h.add('  </footer>')

h.add('</body>')
h.add('</html>')

# Guardar HTML
with open('InfoCircuito.html', 'w', encoding='utf-8') as f:
    f.write(h.render())

print("Archivo InfoCircuito.html generado.")