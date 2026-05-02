---
name: llm-knowledge-base
description: >
  Deploy a personal LLM Knowledge Base following Andrej Karpathy's pattern.
  Creates a 3-layer wiki system (raw sources, LLM-maintained wiki, outputs)
  in any directory. Use this skill whenever the user wants to: create a knowledge base,
  set up a wiki, organize their notes with LLM, build a second brain,
  deploy Karpathy's KB pattern, start a personal wiki, or set up an Obsidian
  knowledge base with Claude. Also use when someone says "knowledge base",
  "LLM wiki", "personal wiki", "second brain with AI", or asks how to
  organize documents so an LLM can maintain them.
---

# LLM Knowledge Base

Deploy a personal LLM Knowledge Base in any directory. Based on Andrej Karpathy's pattern: instead of RAG (re-derive on every query), the LLM incrementally builds and maintains a persistent wiki from raw sources. The wiki compounds over time — every source added and every question asked makes it richer.

Source: https://gist.github.com/karpathy/442a6bf555914893e9891c11519de94f

## Why this exists

Most people use LLMs with documents via RAG: upload files, model searches for relevant chunks per query, generates an answer. Problem: knowledge never accumulates. Each question starts from zero.

The boring part of maintaining a knowledge base isn't reading or thinking — it's bookkeeping. Humans abandon wikis because maintenance costs grow faster than value. LLMs don't get tired and can update 15 files in one pass. This pattern exploits that.

## What gets created

```
your-directory/
├── raw/                  # Immutable source documents (user writes, LLM reads)
│   ├── articles/         # Saved articles, blog posts
│   ├── papers/           # Research papers
│   ├── notes/            # Personal notes, ideas
│   └── images/           # Diagrams, screenshots
├── wiki/                 # LLM-generated wiki (LLM writes, user reads)
│   ├── index.md          # Master catalog of all pages
│   ├── log.md            # Chronological operations log
│   └── _templates/       # Page templates
│       └── entity.md
├── outputs/              # Generated reports, answers, analyses
└── CLAUDE.md             # Schema — the LLM's operating instructions
```

## Setup workflow

### Step 1: Ask the user for context

Before creating anything, ask:

1. **Where?** Which directory to set up in. Default: current working directory.
2. **Domains?** What topics will the KB cover? Examples: AI/ML, finance, health, programming, personal development. These become the domain categories in the wiki index. Default: use 3-4 domains based on what you know about the user. If you know nothing, use: Technology, Projects, Learning, Personal.
3. **Existing content?** Are there already files in the directory that should be treated as raw sources or bootstrapped into wiki pages?

Keep it conversational — don't present a form. If the context is already clear from the conversation, skip redundant questions.

### Step 2: Create the directory structure

Create all directories. Use the script at `scripts/setup.sh` if available, or create manually:

```
raw/articles/
raw/papers/
raw/notes/
raw/images/
wiki/_templates/
outputs/
```

Add a `.gitkeep` in each empty leaf directory so git tracks them.

### Step 3: Create CLAUDE.md

This is the brain of the system. Read the template at `references/claude-md-template.md` and customize it:

- Replace `{{VAULT_PATH}}` with the actual directory path
- Replace the `{{DOMAINS}}` section with the user's chosen domains
- Keep everything else as-is — the template contains the full schema, operations, and conventions

Write the customized CLAUDE.md to the target directory root.

### Step 4: Create wiki/index.md

```markdown
---
type: index
updated: today's date
---

# Wiki Index

Master catalog of all wiki pages. Updated automatically on every Ingest operation.

## By Domain

### Domain 1


### Domain 2


### Domain 3


## Recently Added


## Auto-table (Dataview)

` ` `dataview
TABLE type AS "Type", domain AS "Domain", updated AS "Updated"
FROM "wiki"
WHERE type != null AND type != "index"
SORT updated DESC
` ` `
```

Use the user's chosen domains as section headers.

### Step 5: Create wiki/log.md

```markdown
---
type: log
---

# Operations Log

Chronological log of all knowledge base operations.

## [today's date] init | Knowledge Base created

- Created directory structure: `raw/`, `wiki/`, `outputs/`
- Created `wiki/index.md` — content catalog
- Created `wiki/log.md` — this file
- Created `CLAUDE.md` — knowledge base schema
- Pattern: LLM Knowledge Base (Andrej Karpathy)
```

### Step 6: Create wiki/_templates/entity.md

```markdown
---
type:
domain:
created: {{date}}
updated: {{date}}
sources: []
tags: []
---

# {{title}}

## Summary



## Key Details



## Related



## Sources

```

### Step 7: Bootstrap from existing content (optional)

If the user has existing files:

1. Copy or move them into `raw/` (appropriate subdirectory)
2. Run Ingest on each: read the source, create a wiki page, update index and log
3. Add cross-references between related pages

Even 2-3 starter pages make the KB feel alive and demonstrate the pattern.

### Step 8: Brief the user

After setup, explain:

**Three operations you'll use:**

1. **Ingest** — Drop a file into `raw/`, then tell Claude: "Process the new file in raw/articles/filename.md". Claude reads it, creates/updates wiki pages, updates index and log.

2. **Query** — Ask questions against the wiki: "Based on everything in wiki/, what are the main trends in X?" Claude reads the relevant pages and synthesizes an answer.

3. **Lint** — Monthly health check: "Check all of wiki/. Find contradictions, orphan pages, missing cross-references." Catches errors before they compound.

**How to add sources:**
- **Obsidian Web Clipper** (browser extension) — one click saves any webpage as .md
- **Copy-paste** — create a .md file manually in `raw/articles/` or `raw/notes/`
- **Tell Claude** — "Save this article to raw/" and it will
- **CLI tools** — `yt-dlp` for YouTube transcripts, `summarize` for article summaries

**Quick start:**
1. Find an interesting article
2. Save it to `raw/articles/`
3. Tell Claude: "Process the new file"
4. Read the new wiki pages
5. Ask a question about the topic
6. Repeat

Each cycle takes 2-5 minutes and makes the knowledge base richer.

## Important principles

- **Never modify files in `raw/`** — they are immutable sources of truth
- **The LLM owns `wiki/`** — it creates, updates, and maintains all wiki pages. The user reads them. If the user wants to fix something, they tell the LLM.
- **Keep it flat and simple** — plain markdown files with good schema beats any complex plugin stack
- **Compound your knowledge** — save valuable query answers back into wiki or outputs. Your research compounds together with your sources.
- **Run Lint monthly** — catches errors before they accumulate

## Adapting for non-Obsidian setups

The pattern works with any text editor. Obsidian adds:
- **Graph View** (Cmd/Ctrl+G) — visualizes connections between pages
- **Dataview plugin** — auto-generates tables from frontmatter
- **Web Clipper** — one-click article saving

Without Obsidian, the user still gets the full wiki system — they just browse files in their editor of choice. `[[wiki-links]]` become documentation conventions rather than clickable links.

## Adapting for non-Claude-Code setups

The pattern works with any LLM agent that has file access:
- **Cursor** — same workflow, different IDE
- **Codex** — CLI-based, same commands
- **ChatGPT with file uploads** — manual file management, but the wiki pattern still applies
- **Any MCP-capable agent** — as long as it can read/write files

The key file is CLAUDE.md (rename to AGENTS.md or similar for other tools). It's the schema that teaches the LLM how to operate the knowledge base.
