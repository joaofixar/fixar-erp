"""Gera malhas SVG locais a partir dos dados públicos da API de malhas do IBGE."""
import argparse
import concurrent.futures
import json
import gzip
import math
import pathlib
import ssl
import urllib.request

STATES = dict(RO=11,AC=12,AM=13,RR=14,PA=15,AP=16,TO=17,MA=21,PI=22,CE=23,RN=24,PB=25,PE=26,AL=27,SE=28,BA=29,MG=31,ES=32,RJ=33,SP=35,PR=41,SC=42,RS=43,MS=50,MT=51,GO=52,DF=53)
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('--insecure', action='store_true', help='Somente para baixar dados públicos em ambientes sem a cadeia de certificados')
args = parser.parse_args()
destination = pathlib.Path(__file__).resolve().parents[1] / 'mapas-municipios'
destination.mkdir(exist_ok=True)
context = ssl._create_unverified_context() if args.insecure else ssl.create_default_context()
def generate(entry):
    uf, code = entry
    url = 'https://servicodados.ibge.gov.br/api/v3/malhas/estados/%s?formato=application/vnd.geo+json&qualidade=minima&intrarregiao=municipio' % code
    with urllib.request.urlopen(url, timeout=45, context=context) as response:
        raw = response.read()
        source = json.loads(gzip.decompress(raw) if raw.startswith(b'\x1f\x8b') else raw)
    polygons = []
    points = []
    for feature in source['features']:
        identifier = str(feature['properties']['codarea'])
        assert len(identifier) == 7 and identifier.isdigit() and identifier.startswith(str(code))
        geometry = feature['geometry']
        assert geometry['type'] in ('Polygon', 'MultiPolygon')
        rings = geometry['coordinates'] if geometry['type'] == 'Polygon' else [r for p in geometry['coordinates'] for r in p]
        projected = []
        for ring in rings:
            values = []
            for lon, lat in ring:
                assert math.isfinite(lon) and math.isfinite(lat) and -75 < lon < -25 and -40 < lat < 10
                point = (math.radians(lon), -math.log(math.tan(math.pi / 4 + math.radians(lat) / 2)))
                values.append(point); points.append(point)
            projected.append(values)
        polygons.append((identifier, projected))
    assert points and polygons
    x0, x1 = min(p[0] for p in points), max(p[0] for p in points)
    y0, y1 = min(p[1] for p in points), max(p[1] for p in points)
    scale = 480 / max(x1 - x0, y1 - y0)
    locations = []
    for identifier, rings in polygons:
        path = ''.join('M' + 'L'.join('%.2f,%.2f' % (10+(x-x0)*scale,10+(y-y0)*scale) for x,y in ring) + 'Z' for ring in rings)
        locations.append(dict(id=identifier,path=path))
    result = dict(viewBox='0 0 %.2f %.2f' % (20+(x1-x0)*scale,20+(y1-y0)*scale), locations=locations, origem=[x0,y0], escala=scale, fonte=url)
    (destination / (uf + '.json')).write_text(json.dumps(result,separators=(',',':'))+'\n')
    return uf, len(locations)
with concurrent.futures.ThreadPoolExecutor(max_workers=4) as executor:
    for uf,count in executor.map(generate, STATES.items()): print(uf,count)
