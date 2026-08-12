-- =====================================================================
-- E-Books access: Google-enforced student sign-in
--
-- The website no longer asks visitors to type a VVU email (that check was
-- forgeable — it only looked at the text after the "@"). Access is now
-- decided by Google: the Drive folder is shared with the st.vvu.edu.gh
-- Workspace domain only, so a real student sign-in is required.
--
-- This script only refreshes the on-page wording and the Drive link.
-- The sharing change itself is made in Google Drive, not here.
-- =====================================================================

SET NAMES utf8mb4;

-- 1. Page wording under "VVU Library E-Books".
UPDATE administration_content_fields cf
  JOIN administration_content c ON c.id = cf.content_id
   SET cf.field_value = '<p>Scan the QR code with your mobile device, or use the button below. Google will ask you to sign in with your student account (@st.vvu.edu.gh) before the collection opens.</p>'
 WHERE c.section_key = 'db_qr_ebooks'
   AND cf.field_key  = 'section_description';

UPDATE administration_content_fields cf
  JOIN administration_content c ON c.id = cf.content_id
   SET cf.field_value = 'Access E-Books Collection'
 WHERE c.section_key = 'db_qr_ebooks'
   AND cf.field_key  = 'button_text';

-- 2. The Drive link.
--    Replace the URL below with the share link of the folder in
--    ebenezer-owusu@st.vvu.edu.gh's Drive AFTER you have set its General
--    access to "Valley View University (st.vvu.edu.gh) — Viewer".
--    You can also paste it in the admin panel instead of running this.
--
-- UPDATE administration_content_fields cf
--   JOIN administration_content c ON c.id = cf.content_id
--    SET cf.field_value = 'https://drive.google.com/drive/folders/PUT-NEW-FOLDER-ID-HERE'
--  WHERE c.section_key = 'db_qr_ebooks'
--    AND cf.field_key  = 'ebooks_url';

-- 3. The access log no longer stores an email — nobody's identity reaches
--    this server any more, Google holds it. Allow NULL so the hand-off can
--    still be counted. (The app does this automatically too; this is here
--    for hosts where the web user cannot ALTER tables at runtime.)
SET @ddl = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ebook_access_log') > 0,
    'ALTER TABLE ebook_access_log MODIFY student_email VARCHAR(190) NULL',
    'SELECT 1'));
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- 4. Nothing to do for the QR code. It encodes the ebooks_access.php address,
--    not the Drive link, so changing the Drive folder does not invalidate any
--    printed or cached QR code.
