import { pathToFileURL } from 'node:url'
import path from 'node:path'
import { writeFileSync } from 'node:fs'
import { fileURLToPath } from 'node:url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const entryPath = path.resolve(__dirname, '../dist/server/entry-server.js')
const outPath = path.resolve(__dirname, '../public/static-sitemap-paths.json')

const mod = await import(pathToFileURL(entryPath).href)
const paths = mod.getStaticSitemapPaths()

writeFileSync(outPath, `${JSON.stringify(paths, null, 2)}\n`, 'utf8')
console.log(`Wrote ${paths.length} static sitemap paths to public/static-sitemap-paths.json`)
