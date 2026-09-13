# VVU Scholar — Research Portal

A research-analytics portal for Valley View University, modelled on the kind of
institutional scholar portal UCC runs at `scholar.ucc.edu.gh`: publication and
citation data for every researcher, consolidated in one place and browsable by
person, faculty, research area and year.

Everything visitors see is edited at **Admin → Research Portal (VVU Scholar)**.

---

## Where the files are

All public pages live in `research/`, as requested.

| Path | What it is |
|---|---|
| `research/index.php` | Landing page — hero, live stats, trend chart, leaderboard, faculties, areas, featured publications, grants, partners, CTA |
| `research/authors.php` | Researcher directory — search, filter by faculty/area, sort by any metric, paged |
| `research/publications.php` | Publication catalogue — full-text search, filter by faculty/area/type/year/open-access, paged |
| `research/units.php` | Faculties and schools, with a ranked comparison table |
| `research/areas.php` | Thematic research areas, with SDG alignment |
| `research/scholar.php?p=<slug>` | Researcher profile — metrics, own output chart, interests, grants, co-authors, publication list |
| `research/api.php` | Read-only JSON: `suggest`, `scholars`, `publications`, `stats` |
| `research/includes/research_helper.php` | Data layer — every query the pages run |
| `research/includes/research_partials.php` | Shared markup (person card, publication row, pager, sub-nav) |
| `research/assets/research.css` | Portal stylesheet, scoped under `.vvus` |
| `research/assets/research.js` | Counters, live search, copy-citation, charts, reveal-on-scroll |

Admin and schema:

| Path | What it is |
|---|---|
| `admin/manage_research.php` | The CMS — ten tabs, linked from the sidebar |
| `admin/includes/research_import.php` | Crossref / ORCID / BibTeX / CSV / Google Scholar / ResearchGate importers |
| `sql/research_portal_schema.sql` | The eleven tables, plus the seed stat tiles, section headings and page copy |
| `sql/research_portal_units.sql` | The real academic structure — four faculties/schools, seven departments |
| `sql/research_portal_openalex.sql` | The two columns that make an OpenAlex import re-runnable |
| `sql/import_openalex.php` | Loads the University's real corpus from saved OpenAlex pages |
| `sql/assign_units_openalex.php` | Places researchers in faculties from their own stated affiliations |

---

## Install

```bash
mysql -u root valley_view_uni < sql/research_portal_schema.sql    # tables + page copy
mysql -u root valley_view_uni < sql/research_portal_units.sql     # faculties & departments
mysql -u root valley_view_uni < sql/research_portal_openalex.sql  # import bookkeeping columns
```

All three are idempotent — re-running them never drops or duplicates data.

Then load the corpus. On a server with internet access, the easiest route is
**Admin → Research Portal → Import Data → OpenAlex**, which needs no arguments:
it is pre-filled with Valley View University's OpenAlex id and returns the whole
institutional output with citation counts attached.

Then open `/research/` and `Admin → Research Portal`.

---

## Where the data comes from

The catalogue is the University's real published output, indexed from
[OpenAlex](https://openalex.org) — the open catalogue of scholarly works — for
every record carrying a Valley View University affiliation
(OpenAlex institution `I3133169337`).

Nothing in the catalogue is invented. Each record's title, byline, journal,
year, volume, pages, DOI, open-access status and citation count is the value
OpenAlex returned.

**As first loaded:** 775 publications, 557 VVU-affiliated researchers,
3,885 citations, institutional h-index 31, 75% open access, spanning 1971–2026.

### Faculty assignment

OpenAlex does not record which faculty someone belongs to — but the papers do.
Each authorship carries the affiliation line exactly as it was printed, and most
VVU lines name the department:

```
Department of Accounting and Finance, Valley View University, Oyibi, Accra, Ghana
School of Nursing and Midwifery, Valley View University, Oyibi, Ghana
```

`sql/assign_units_openalex.php` reads those lines and matches them against the
University's current structure. It is therefore derived from what the authors
themselves published, not guessed from their subject area. **319 of 537**
researchers matched; the rest state no more than "Valley View University" and
are left unassigned for an editor to place.

Re-running it never overwrites an editor's correction unless `--force` is given.

### Research areas

The nine areas are derived from the subject fields that actually appear in VVU
output, so every area has real work behind it:

| Area | Publications |
|---|---:|
| Social Sciences & Development | 169 |
| Business, Management & Accounting | 154 |
| Health & Medicine | 136 |
| Economics & Finance | 67 |
| Computing & Information Systems | 67 |
| Arts & Humanities | 57 |
| Psychology & Behaviour | 41 |
| Environment, Agriculture & Life Sciences | 40 |
| Engineering & Technology | 37 |

### Two caveats worth knowing

- **Researcher profiles are affiliation-derived.** A profile exists for anyone
  who published *with a VVU affiliation*, which includes former staff and
  visitors as well as current faculty. Each has a name, ORCID where one is
  recorded, and their real publication list — but no position, photograph or
  biography until an editor adds one. Use the **Active** switch to hide any
  profile that should not be listed.
- **OpenAlex occasionally mis-attributes.** A handful of records match
  "Valley View" for reasons unrelated to the University. Anything that looks
  wrong can be unpublished on the Publications tab.

### Refreshing later

Re-run the OpenAlex import from the admin whenever you want current citation
counts. Records are matched on their OpenAlex id first, then DOI, then title and
year, so a re-run updates what is there and adds what is new — it does not
duplicate. Finish with **Recount metrics** to rebuild the rankings.

---

## The admin panel

`Admin → Research Portal (VVU Scholar)` — ten tabs.

| Tab | Edits |
|---|---|
| **Page Content** | Hero badge/headline/lead/paragraph/background image, search placeholder, metrics note, leaderboard size, featured-publication count, trend years, CTA, research-office contact, SEO title & description |
| **Sections** | The heading, eyebrow, intro and display order of every band on the landing page — and an on/off switch per band |
| **Stat Tiles** | The headline figures. Each tile is either a **live count** (researchers, publications, citations, h-index, faculties, areas, open-access %, published this year, partners) or a fixed number you type |
| **Researchers** | Full CRUD — name, position, faculty, photo, biography, interests, research areas, Google Scholar / ORCID / ResearchGate / Scopus / LinkedIn / website, and the citation metrics |
| **Publications** | Full CRUD — title, byline, lead author, faculty, area, type, venue, year, volume/issue/pages, DOI, URL, PDF, abstract, keywords, citations, open-access and featured flags, and VVU co-authors |
| **Faculties** | Faculties, schools, departments and centres — name, type, parent faculty, description, dean, icon, accent colour, link |
| **Research Areas** | Themes, with descriptions, icons, colours and UN SDG numbers |
| **Highlights** | Grants, awards, projects, spotlights, partnerships and facilities |
| **Partners** | Collaborating institutions and funders |
| **Import Data** | Bulk-load publications — see below |

Two buttons sit above the tabs:

- **View portal** — opens `/research/` in a new tab.
- **Recount metrics** — rebuilds every researcher's citations, h-index, i10-index
  and publication count from the publications actually in the catalogue. Run it
  after a bulk import, or any time the leaderboard looks out of step with the
  papers underneath it.

### Sections can be switched off

Turning a section off on the **Sections** tab removes that whole band from the
landing page. The page stays coherent however many are on — there is no layout
that depends on a particular band being present.

### Live vs. typed figures

A stat tile with a **Counted from** value set is computed on every page load
from the catalogue and shows a small green *Live* marker. A tile left on
*"A fixed value I type below"* shows exactly what you type. The institutional
h-index is always computed — it is the largest *h* for which *h* publications
have each been cited at least *h* times — so it can never contradict the
publication list below it.

---

## Getting data in

Six importers, in descending order of how reliably they work from a server.

### 0. OpenAlex — start here

The only source that resolves the institution itself, so one run returns the
University's whole output rather than whatever an affiliation string happens to
match. Open API, no key, and citation counts are included. Pre-filled with VVU's
OpenAlex id; narrow it to one researcher or one year range if you want.

### 1. Crossref — recommended

Open API, no key, and the **only source here that returns citation counts**
(`is-referenced-by-count`). Search by author, affiliation, title or a single
DOI. Also fills in the byline, journal, pagination, abstract and an open-access
flag (from a Creative Commons licence on the full text).

### 2. ORCID — recommended

Open API, no key. Authoritative for *what a person wrote*, but ORCID carries no
citation counts, so leave **"Look each DOI up in Crossref"** ticked and each
record comes back complete. Capped at 25 enrichment lookups per import so one
run cannot time the page out.

### 3. BibTeX / 4. CSV — always work

These are what Google Scholar's own **Export** button produces. Open the profile
in your browser, export, and paste the file (or upload it). The BibTeX parser
handles nested braces, single-line entries, and re-orders `Surname, Forename and
…` bylines into reading order. The CSV parser matches column names loosely, so
Scholar's own header and a hand-made spreadsheet both work.

### 5. Google Scholar / ResearchGate — best effort

Google serves a CAPTCHA to most hosted servers, and ResearchGate blocks
automated readers outright. When the direct fetch fails you are told so plainly
and pointed at BibTeX/CSV — it will not fail silently. The **Sync metrics from
Google Scholar** button on a researcher's record uses the same path.

ResearchGate's terms do not permit harvesting its listings, so that importer
only validates and stores the profile URL (which then appears on the
researcher's page); their publications come from Crossref, ORCID or BibTeX.

### The import review step

Nothing is written straight to the database. Every import shows a preview table
first, with each row marked **New** or **Already indexed**. Duplicates are
matched on DOI first, then on a normalised title + year, so the same paper
arriving from Scholar and from Crossref is recognised as one record. You choose
which rows to import, which researcher / faculty / area to credit them to, and
whether existing records should have their citation counts refreshed.

After the import the credited researcher's totals are recounted automatically.

---

## Notes for whoever maintains this

**Sub-directory support.** `includes/header.php` and `includes/footer.php` now
honour a `$vvu_root` variable set before they are included. `research/*.php`
sets `$vvu_root = '../'`, and every asset, nav link and CMS-managed URL resolves
through it. Pages at the document root set nothing and are unaffected.

**CSS scoping.** The stylesheet is scoped under `.vvus`. The site still loads
bootstrap.css, materialize.css and style.css, which between them set
`html{font-size:10px}`, decorate bare `<input>`s, and give `<nav>` a salmon
background and a fixed height. `research.css` neutralises those at the top and
then styles from scratch; element-level resets are wrapped in `:where()` so they
never out-specify the component rules below them.

**Departments nest inside faculties** via `research_units.parent_id`. A faculty's
figures absorb its departments' — "Faculty of Science" includes everything filed
under Computing Sciences and under Nursing and Health Sciences — and filtering
the directory by a faculty includes everyone in its departments. Departments are
listed inside their faculty's card rather than beside it, so nothing is counted
twice on screen.

**Co-authorship** is what makes a paper appear on every VVU author's profile
rather than only the lead author's, and it drives the "Publishes with" panel.
Tick the co-authors on the publication form; the lead author is included
automatically.

**Security.** `research/includes/` is blocked over HTTP by `.htaccess`. The API
is read-only. Every admin write is CSRF-checked, and delete links carry the
session token. The two `ORDER BY` clauses that have to interpolate do so from a
fixed whitelist; everything else is a prepared statement.

**Not yet done:** the portal is not linked from the site navigation. Add it at
`Admin → Navigation Settings` pointing at `research/index.php` — wherever in the
menu it belongs is an editorial decision.
