-- =====================================================================
--  Research portal clean-up (2026-10)
--
--  1. Hides three publications dated before Valley View University was
--     founded (1979). They were attached by the OpenAlex import to people
--     who are not VVU staff, and made "Years covered" read 1971–2026.
--  2. Removes duplicate papers: the same paper listed 2–4 times (preprint
--     + journal version, or several Zenodo/Research Square version DOIs).
--     In each group the published / most-cited version is kept, it gets
--     the highest citation count of the group and every linked author,
--     and the extra copies are HIDDEN (is_active = 0), not deleted — so
--     they can be restored, and future imports will not re-add them.
--
--  Rows are matched by DOI, so this works on any copy of the database.
--  Safe to re-run. Import in phpMyAdmin (Import tab) on the live site.
-- =====================================================================

SET NAMES utf8mb4;

-- 1. Pre-1979 records that are not VVU research
UPDATE research_publications SET is_active = 0
 WHERE LOWER(doi) IN ('10.1090/qam/281407',
                      '10.1097/00017285-197303000-00009',
                      '10.1080/00207237808737363')
   AND pub_year < 1979;

-- 2. Duplicate papers

-- KEEP  [235] 2020 Quality of nursing education programme in the Philippines: faculty members perspectives  (10.1186/s12912-020-00508-9, BMC Nursing)
-- hide  [579] 2020 10.21203/rs.3.rs-25420/v1  (Research Square)
-- hide  [684] 2020 10.21203/rs.3.rs-25420/v2  (Research Square)
-- hide  [690] 2020 10.21203/rs.3.rs-25420/v3  (Research Square)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.1186/s12912-020-00508-9' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.21203/rs.3.rs-25420/v1', '10.21203/rs.3.rs-25420/v2', '10.21203/rs.3.rs-25420/v3')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.21203/rs.3.rs-25420/v1', '10.21203/rs.3.rs-25420/v2', '10.21203/rs.3.rs-25420/v3') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.21203/rs.3.rs-25420/v1', '10.21203/rs.3.rs-25420/v2', '10.21203/rs.3.rs-25420/v3') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [246] 2023 Akan ethnomathematics: Demonstrating its pedagogical action on the teaching and learning o  (10.29333/mathsciteacher/13281, Journal of Mathematics and Science Teacher)
-- hide  [743] 2021 10.18860/ijtlm.v4i2.10264  (International Journal on Teaching and Learning Mathematics)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.29333/mathsciteacher/13281' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.18860/ijtlm.v4i2.10264')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.18860/ijtlm.v4i2.10264') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.18860/ijtlm.v4i2.10264') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [249] 2025 Knowledge, uptake and therapeutic effectiveness of sulfadoxine-pyrimethamine (IPTp-SP) amo  (10.1186/s12884-024-07089-4, BMC Pregnancy and Childbirth)
-- hide  [732] 2023 10.21203/rs.3.rs-3686754/v1  (Research Square)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.1186/s12884-024-07089-4' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.21203/rs.3.rs-3686754/v1')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.21203/rs.3.rs-3686754/v1') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.21203/rs.3.rs-3686754/v1') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [327] 2012 From poverty to poor health: Analysis of socio-economic pathways influencing health status  (10.5172/hesr.2012.21.2.232, Health Sociology Review)
-- hide  [687] 2012 10.5172/hesr.2012.830  (Health Sociology Review)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5172/hesr.2012.21.2.232' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5172/hesr.2012.830')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5172/hesr.2012.830') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5172/hesr.2012.830') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [350] 2019 ONTOLOGY OF UBIQUITOUS LEARNING: WHATSAPP MESSENGER COMPETES SUCCESSFULLY WITH LEARNING MA  (10.33965/icedutech2019_201902l009, )
-- hide  [419] 2019 10.1007/978-3-030-17798-0_11  (Advances in intelligent systems and computing)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.33965/icedutech2019_201902l009' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.1007/978-3-030-17798-0_11')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.1007/978-3-030-17798-0_11') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.1007/978-3-030-17798-0_11') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [371] 2023 Female students’ perspectives on cervical cancer screening inclusion in pre-admission un  (10.1371/journal.pgph.0001029, PLOS Global Public Health)
-- hide  [712] 2023 10.21428/3d48c34a.d0afe857  ()
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.1371/journal.pgph.0001029' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.21428/3d48c34a.d0afe857')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.21428/3d48c34a.d0afe857') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.21428/3d48c34a.d0afe857') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [401] 2025 Sustainable mini-grid model as an alternative energy source for deprived communities in Gh  (10.1080/19376812.2025.2563322, African Geographical Review)
-- hide  [733] 2023 10.21203/rs.3.rs-3720125/v1  (Research Square)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.1080/19376812.2025.2563322' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.21203/rs.3.rs-3720125/v1')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.21203/rs.3.rs-3720125/v1') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.21203/rs.3.rs-3720125/v1') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [628] 2017 GENDER VARIATION IN READING HABITS IN SCHOOLS IN MOLAND: A CASE STUDY OF ASANTEKWAA S.D. A  (10.46827/ejes.v0i0.719, Open Access Publishing Group - European Journal of Education Studies)
-- hide  [427] 2017 10.5281/zenodo.573273  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.46827/ejes.v0i0.719' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.573273')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.573273') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.573273') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [429] 2019 Gender Inequality in the Usage of ICT Facilities in Academic Libraries: A Case Study of Pr  (10.53935/2641-533x.v2i4.123, International Journal of Educational Studies)
-- hide  [692] 2019 10.7176/jep/10-25-06  ()
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.53935/2641-533x.v2i4.123' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.7176/jep/10-25-06')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.7176/jep/10-25-06') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.7176/jep/10-25-06') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [471] 2017 USING PHONIC METHOD TO IMPROVE POOR READING ABILITY OF PUPILS AT TECHIMAN SENIOR HIGH SCHO  (10.46827/ejes.v0i0.803, Open Access Publishing Group - European Journal of Education Studies)
-- hide  [469] 2017 10.5281/zenodo.806824  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.46827/ejes.v0i0.803' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.806824')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.806824') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.806824') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [535] 2020 Reading Isaiah 2:1-5 in the Light of YHWH’S Mission: A Linguistic Analysis  (10.47987/huxo6558, All Nations University Journal of Applied Thought)
-- hide  [556] 2020 10.47987/uckn5876  (All Nations University Journal of Applied Thought)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.47987/huxo6558' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.47987/uckn5876')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.47987/uckn5876') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.47987/uckn5876') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [542] 2024 Mapping the Quantum Frontier in Decision-making: A Comprehensive Review on the Implication  (10.24940/theijbm/2023/v11/i12/ojsbm2311-001, The International Journal of Business & Management)
-- hide  [737] 2023 10.6007/ijarbss/v13-i12/20389  (International Journal of Academic Research in Business and Social Sciences)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.24940/theijbm/2023/v11/i12/ojsbm2311-001' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.6007/ijarbss/v13-i12/20389')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.6007/ijarbss/v13-i12/20389') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.6007/ijarbss/v13-i12/20389') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [558] 2021 The Impact of Computerization on Financial Reporting Practice: The Perspectives of Interna  (10.4236/ojacct.2021.103009, Open Journal of Accounting)
-- hide  [671] 2021 10.2139/ssrn.3800429  (SSRN Electronic Journal)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.4236/ojacct.2021.103009' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.2139/ssrn.3800429')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.2139/ssrn.3800429') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.2139/ssrn.3800429') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [701] 2023 Women in Sports Development : A Study on the Ministry of Youth and Sports  (10.47191/ijsshr/v6-i1-101, International Journal of Social Science and Human Research)
-- hide  [702] 2023 10.5281/zenodo.7593898  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.47191/ijsshr/v6-i1-101' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.7593898')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.7593898') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.7593898') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [755] 2024 The effect of institutional factors on passport services digitalisation in an emerging cou  (10.1504/eg.2025.10067337, Electronic Government an International Journal)
-- hide  [809] 2025 10.1504/eg.2025.148251  (Electronic Government an International Journal)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.1504/eg.2025.10067337' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.1504/eg.2025.148251')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.1504/eg.2025.148251') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.1504/eg.2025.148251') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [758] 2024 Dynamic analysis of Q-fever transmission among cattle in the Tropical Savannah Grassland z  (10.1101/2024.11.15.623876, bioRxiv (Cold Spring Harbor Laboratory))
-- hide  [759] 2024 10.22541/au.173199980.05369711/v1  ()
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.1101/2024.11.15.623876' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.22541/au.173199980.05369711/v1')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.22541/au.173199980.05369711/v1') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.22541/au.173199980.05369711/v1') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [837] 2020 Advances in Deep Neural Architectures for Generalizable Learning  (10.5281/zenodo.17745880, Zenodo (CERN European Organization for Nuclear Research))
-- hide  [838] 2020 10.5281/zenodo.17745881  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.17745880' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.17745881')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.17745881') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.17745881') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [843] 2025 Analysis of the Nexus Between Employee Relations and Organizational Performance: A Review   (10.55677/ijhrsss/03-2025-vol02i12, International journal of human research and social science studies.)
-- hide  [840] 2025 10.5281/zenodo.17908314  (International journal of human research and social science studies.)
-- hide  [841] 2025 10.5281/zenodo.17908671  (International journal of human research and social science studies.)
-- hide  [842] 2025 10.5281/zenodo.17908315  (International journal of human research and social science studies.)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.55677/ijhrsss/03-2025-vol02i12' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.17908314', '10.5281/zenodo.17908671', '10.5281/zenodo.17908315')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.17908314', '10.5281/zenodo.17908671', '10.5281/zenodo.17908315') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.17908314', '10.5281/zenodo.17908671', '10.5281/zenodo.17908315') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [847] 2025 Effects of Audio-Visual Aids on Junior High School Students' Academic Achievement in Socia  (10.5281/zenodo.18071367, Zenodo (CERN European Organization for Nuclear Research))
-- hide  [848] 2025 10.5281/zenodo.18071366  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.18071367' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.18071366')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.18071366') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.18071366') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [873] 2026 The Work and Study Nexus: Balancing Work and Study at Valley View University, Oyibi Campus  (10.61538/jipe.v17i2.1976, JOURNAL OF ISSUES AND PRACTICE IN EDUCATION)
-- hide  [932] 2026 10.61538/jipe.v17i2.2142  (JOURNAL OF ISSUES AND PRACTICE IN EDUCATION)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.61538/jipe.v17i2.1976' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.61538/jipe.v17i2.2142')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.61538/jipe.v17i2.2142') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.61538/jipe.v17i2.2142') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [881] 2026 Addressing the Roots of School Bullying: Contributing Factors and Administrative Responses  (10.37745/bje.2013/vol14n294108, British Journal of Education)
-- hide  [924] 2026 10.7176/jep/17-6-12  ()
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.37745/bje.2013/vol14n294108' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.7176/jep/17-6-12')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.7176/jep/17-6-12') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.7176/jep/17-6-12') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [887] 2026 Green Accounting and Financial Performance of Rural Banks in Ghana: Exploring the Mediatin  (10.2139/ssrn.6643255, SSRN Electronic Journal)
-- hide  [947] 2026 10.2139/ssrn.7232675  (SSRN Electronic Journal)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.2139/ssrn.6643255' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.2139/ssrn.7232675')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.2139/ssrn.7232675') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.2139/ssrn.7232675') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [889] 2025 EFFECTS OF CAREER EXPLORATION PROGRAMMES ON CAREER MATURITY AMONG SENIOR HIGH SCHOOL STUDE  (10.5281/zenodo.19846602, Zenodo (CERN European Organization for Nuclear Research))
-- hide  [890] 2025 10.5281/zenodo.19846599  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.19846602' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.19846599')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.19846599') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.19846599') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [900] 2025 Hooked or Empowered? The Dilemma of Mobile Phone Use among Asantekwaa SDA Junior High Scho  (10.5281/zenodo.19705429, International Research Journal of Science, Technology, Education, and Management)
-- hide  [901] 2025 10.5281/zenodo.19705428  (International Research Journal of Science, Technology, Education, and Management)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.19705429' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.19705428')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.19705428') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.19705428') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [928] 2026 A Biblical Network Investment Framework for Financial Sustainability in Seventh-day Advent  (10.5281/zenodo.21344799, Zenodo (CERN European Organization for Nuclear Research))
-- hide  [929] 2026 10.5281/zenodo.21344798  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.21344799' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.21344798')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.21344798') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.21344798') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [930] 2026 Toward a Sustainable Communal Development Fund Institution: A Grounded Theory Model from G  (10.5281/zenodo.21343627, Zenodo (CERN European Organization for Nuclear Research))
-- hide  [931] 2026 10.5281/zenodo.21343628  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.21343627' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.21343628')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.21343628') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.21343628') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [963] 2026 Beyond The Syllabus: Addressing Hidden Curriculum for Equity and Excellence in African Hea  (10.5281/zenodo.22297048, Zenodo (CERN European Organization for Nuclear Research))
-- hide  [964] 2026 10.5281/zenodo.22297047  (Zenodo (CERN European Organization for Nuclear Research))
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.5281/zenodo.22297048' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.5281/zenodo.22297047')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.5281/zenodo.22297047') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.5281/zenodo.22297047') AND @keep IS NOT NULL AND id <> @keep;

-- KEEP  [965] 2026 Digital Assessment Acceptance among Nursing and Midwifery Students in Ghana: Development a  (10.17632/mnggf98w2r, Mendeley Data)
-- hide  [966] 2026 10.17632/mnggf98w2r.1  (Mendeley Data)
SET @keep := (SELECT id FROM research_publications WHERE LOWER(doi) = '10.17632/mnggf98w2r' ORDER BY id LIMIT 1);
UPDATE research_publications k JOIN (SELECT MAX(citations) c FROM research_publications WHERE LOWER(doi) IN ('10.17632/mnggf98w2r.1')) d SET k.citations = GREATEST(k.citations, d.c) WHERE k.id = @keep;
INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) SELECT @keep, a.scholar_id, a.author_order FROM research_publication_authors a JOIN research_publications p ON p.id = a.publication_id WHERE LOWER(p.doi) IN ('10.17632/mnggf98w2r.1') AND @keep IS NOT NULL AND NOT EXISTS (SELECT 1 FROM research_publication_authors x WHERE x.publication_id = @keep AND x.scholar_id = a.scholar_id);
UPDATE research_publications SET is_active = 0 WHERE LOWER(doi) IN ('10.17632/mnggf98w2r.1') AND @keep IS NOT NULL AND id <> @keep;


-- End: 32 duplicate copies hidden across 28 papers.
