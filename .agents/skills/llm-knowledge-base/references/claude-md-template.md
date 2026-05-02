# CLAUDE.md Template

This is the template for the CLAUDE.md file that gets created in the user's knowledge base. Replace all `{{PLACEHOLDER}}` values during setup.

---

# Claude Guidelines

This document provides context and guidelines for Claude when working in this directory.

## Project Context

This is a personal LLM Knowledge Base located at `{{VAULT_PATH}}`.
It follows Andrej Karpathy's pattern for building persistent wikis with LLM assistance.

Source: https://gist.github.com/karpathy/442a6bf555914893e9891c11519de94f

## Working Principles

- **File Organization**: Maintain the 3-layer architecture (raw → wiki → outputs)
- **Markdown Standards**: Follow consistent markdown formatting throughout
- **Links**: Use Obsidian-style wiki links `[[note-name]]` for internal references
- **Tags**: Use appropriate tags for categorization and discovery
- **Metadata**: Every wiki page must have valid YAML frontmatter
- **Simplicity**: Keep it flat and simple — plain files with good schema

---

## Knowledge Base Schema

### Architecture (3 layers)

1. **`raw/`** — Immutable source documents. Articles, papers, images, notes. The LLM reads from here but NEVER modifies these files. This is the source of truth.
   - `raw/articles/` — saved articles, blog posts, web content
   - `raw/papers/` — research papers, academic work
   - `raw/notes/` — personal notes, ideas, meeting notes
   - `raw/images/` — diagrams, screenshots, reference images

2. **`wiki/`** — LLM-generated and maintained wiki. The LLM owns this layer entirely. It creates pages, updates them when new sources arrive, maintains cross-references, and keeps everything consistent. The user reads it; the LLM writes it.
   - `wiki/index.md` — master catalog of all wiki pages (content-oriented)
   - `wiki/log.md` — chronological operations log (time-oriented)
   - `wiki/_templates/` — page templates

3. **`outputs/`** — Generated reports, answers, analyses. Products of Query operations that may later be filed back into wiki.

### Frontmatter Schema

Every wiki page MUST have this frontmatter:

```yaml
---
type: entity | concept | project | person | summary | comparison | index | log
domain: {{DOMAIN_LIST}}
created: YYYY-MM-DD
updated: YYYY-MM-DD
sources:
  - "[[raw/articles/filename]]"
tags: []
---
```

### Naming Conventions

- Wiki pages: `lowercase-kebab-case.md` (e.g., `wiki/machine-learning-basics.md`)
- Raw articles: `YYYY-MM-DD-article-title.md` (e.g., `raw/articles/2026-04-06-interesting-paper.md`)
- Raw notes: descriptive name, no date prefix required
- Outputs: `YYYY-MM-DD-type-description.md` (e.g., `outputs/2026-04-06-research-summary.md`)

### Wiki Link Conventions

- Link between wiki pages: `[[wiki/page-name]]`
- Link to raw sources: `[[raw/articles/filename]]`
- Link to outputs: `[[outputs/filename]]`
- Every wiki page should have a "Related" section with links to other relevant pages

---

## Operations

### Ingest (Processing new sources)

When a new file is added to `raw/`:

1. Read the source document fully
2. Discuss key takeaways with the user (if interactive)
3. Create or update relevant wiki pages in `wiki/`
4. Update `wiki/index.md` — add entries under the appropriate domain section
5. Append an entry to `wiki/log.md` with format: `## [YYYY-MM-DD] ingest | Source Title`
6. Add cross-references (`[[links]]`) between related wiki pages

A single source may touch 5-15 wiki pages. Prefer updating existing pages over creating duplicates.

### Query (Answering questions)

1. Read `wiki/index.md` first to find relevant pages
2. Read the relevant wiki pages
3. Synthesize an answer with citations to wiki pages and raw sources
4. If the answer is valuable — save it to `outputs/` or file it back into wiki as a new page
5. Log the query in `wiki/log.md`: `## [YYYY-MM-DD] query | Question summary`

### Lint (Health check)

Run periodically to maintain wiki quality:

1. Find contradictions between wiki pages
2. Find stale claims superseded by newer sources
3. Find orphan pages (no inbound links)
4. Find concepts mentioned but lacking their own page
5. Find missing cross-references
6. Suggest new questions to investigate or sources to add
7. Log the lint in `wiki/log.md`: `## [YYYY-MM-DD] lint | Summary of findings`

---

## Domains of Interest

{{DOMAINS}}

## Notes

- All changes must be compatible with Obsidian's markdown syntax
- The LLM writes and maintains the wiki; the user curates sources and asks questions
- Never modify files in `raw/` — they are immutable sources of truth
- Keep it simple and flat (Karpathy: "super simple and flat")

---

Last updated: {{TODAY}}
