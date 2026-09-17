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
| `admin/includes/research_openalex_sync.php` | The OpenAlex synchroniser — one engine behind both the admin button and the CLI |
| `sql/import_openalex.php` | Command-line front door to that engine, for servers without internet |

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

Every sync reads those lines and matches them against the University's current
structure, so placement is derived from what the authors themselves published,
not guessed from their subject area. **319 of 537** researchers matched; the
rest state no more than "Valley View University" and are left unassigned for an
editor to place.

A refresh only fills in a faculty for someone who does not have one, so an
editor's correction is never overwritten — unless the "re-assign faculties
already set" box is ticked (or `--force-units` is passed on the command line).

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

### Keeping it up to date

**Admin → Research Portal → Import Data → Refresh from OpenAlex.**

Set *Published from* to last year, leave *Pages this run* at 3, press the button.
That is the whole routine.

A refresh does the complete job, not just the publications:

- adds papers published since the last run;
- updates the citation count on every record it sees;
- creates profiles for researchers new to the portal;
- links co-authors, so a paper appears on each VVU author's profile;
- tags each paper with a research area;
- places people in faculties from the affiliation printed on their own papers;
- rebuilds the cached citation totals the rankings read.

Everything is keyed on OpenAlex ids, so running it twice changes nothing the
second time. Editorial work — positions, photographs, biographies, featured
flags, hidden records, corrected faculties — is never overwritten. The
"re-assign faculties already set" checkbox is the only thing that touches a
faculty an editor has chosen, and it is off by default.

If a run hits the page budget or the host's time limit it stops cleanly, saves
its position and offers a **Continue** button that resumes from exactly there
rather than starting over.

**How often:** once a term is plenty for a catalogue this size. Citation counts
drift slowly; new papers appear in OpenAlex within days to a few weeks of
publication.

**At the turn of the year** there is nothing special to do. The year filter is a
*from* date, not a range, so `2025` keeps picking up 2026 papers as they appear.
The trend chart, the "published this year" figure and the year filter all read
the catalogue directly and roll over on their own. The one thing worth doing
each January is a single wider run — clear *Published from* and set *Pages this
run* to 10 — which re-scans the whole corpus and catches anything back-dated or
indexed late.

### Command-line alternative

Where the server cannot reach the API, fetch the responses elsewhere and run:

```bash
php sql/import_openalex.php sql/openalex               # same engine as the button
php sql/import_openalex.php sql/openalex --dry-run     # count, write nothing
php sql/import_openalex.php sql/openalex --force-units # also re-assign set faculties
```

The button and the script call the same synchroniser
(`admin/includes/research_openalex_sync.php`), so they cannot drift apart.

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

## What the automated sources miss

OpenAlex gave the portal its corpus, but it is not complete, and it is worth
knowing exactly *how* it is incomplete — the gaps have three different causes
and three different remedies.

**1. The journal is not indexed anywhere.** A good deal of Ghanaian and
pan-African scholarship appears in journals that deposit no metadata with
Crossref and are unknown to OpenAlex — *International Journal of Economic
Perspectives*, *International Journal of Economics, Commerce & Management*,
*Journal of Emerging Trends and Novel Research* and others like them. No
refresh will ever find these. They have to be typed in, or pasted as BibTeX/CSV.

**2. The paper is indexed, but not attributed to Valley View.** This is the
larger and less obvious gap. A paper is in Crossref with a DOI, but its
affiliation line names only the author's department, or an earlier employer, or
nothing at all — so the OpenAlex institution query that drives the refresh never
returns it. The fix is to search by *person* rather than by institution: an
ORCID import for that researcher, or a Crossref author search.

**3. The paper is indexed and attributed, but the author is a fresh profile.**
OpenAlex creates a new author record whenever a byline is spelled differently,
so one person can end up as several profiles, each holding a slice of their
work. See *Merging duplicate profiles* below.

### Back-filling DOIs and citation counts

Records typed in by hand carry a title and a citation but no DOI and no citation
count, which leaves them out of the citation metrics and the open-access figure.
`sql/enrich_from_crossref.php` looks each one up in Crossref by title and fills
in what it finds:

```bash
php sql/enrich_from_crossref.php                     # dry run, everything with no DOI
php sql/enrich_from_crossref.php --write             # apply
php sql/enrich_from_crossref.php --write --limit=50  # in batches
php sql/enrich_from_crossref.php --scholar=45        # one researcher
```

It only fills blanks and only ever raises a citation count, so it cannot
overwrite an editor's correction, and it is safe to re-run — anything it
matched already has a DOI and is skipped next time. A title match must reach
93% similarity (`--min=` to change it) and the years must agree to within one,
since a paper's online year and its issue year often differ.

Run **Admin → Research Portal → Recount metrics** afterwards.

### Merging duplicate profiles

One researcher spread across several profiles splits their publication list and
their citation total, and the directory lists them several times. To merge,
point the publications and co-authorships at the profile you are keeping, then
deactivate the rest:

```sql
-- Keep $KEEP, fold $DUPE into it.
UPDATE research_publications        SET scholar_id = $KEEP WHERE scholar_id = $DUPE;
UPDATE IGNORE research_publication_authors SET scholar_id = $KEEP WHERE scholar_id = $DUPE;
DELETE FROM research_publication_authors   WHERE scholar_id = $DUPE;
UPDATE research_scholars SET is_active = 0 WHERE id = $DUPE;
```

`UPDATE IGNORE` then `DELETE` is deliberate: where both profiles are already on
the same paper the re-point would collide with the primary key, so the ignored
rows are dropped afterwards. Keep the profile that has the ORCID and the most
publications. Then **Recount metrics**.

Find the candidates with:

```sql
SELECT full_name, COUNT(*) n, GROUP_CONCAT(id ORDER BY publications_count DESC)
  FROM research_scholars GROUP BY LOWER(TRIM(full_name)) HAVING n > 1;
```

Identical names are safe to merge on sight. Same surname and initial is only a
hint — *Jeanette Owusu* and *Joseph Owusu* are two people, and so are the four
different S. Boatengs. Check the publication lists before merging those.

### Research Office submissions

`sql/research_publications_ocansey_2026.sql` is the worked example: 53 citations
supplied as a Word document, checked against the catalogue, 26 found to be
missing and inserted with their co-authorship links. The file is idempotent —
each row is keyed on an `external_id` and guarded by `NOT EXISTS`, so re-running
it inserts nothing twice. Follow the same shape for the next submission.

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
