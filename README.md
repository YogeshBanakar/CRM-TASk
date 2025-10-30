# CRM-TASk
Basic CRM Features with Merging Contacts with Custom Fields
# Laravel CRM with Contact Merge & Dynamic Fields

> **Senior Laravel Developer Task** – Yogesh Banakar  
> **Tech Stack**: Laravel 10, MySQL, jQuery, Bootstrap 5  
> **Video Demo**: https://drive.google.com/file/d/13MRtLhmrXmYgKkV1ajAeAKtfbf03leSF/view?usp=drive_link

---

## Features Implemented (100% Match to Requirements)

| Feature | Status | Details |
|-------|--------|-------|
| **CRUD with AJAX** | Done | Create, Edit, Delete via modal. No page reload. |
| **Dynamic Custom Fields** | Done | Admin panel to add fields (Text, Date). Saved in `contact_custom_values`. |
| **File Uploads** | Done | Profile image + additional file (PDF/DOC). |
| **Search & Filter (AJAX)** | Done | Name, Email, Gender. Real-time. |
| **Merge Contacts** | Done | Select Master → Preview diff → Confirm. |
| **Merge Preview** | Done | Shows what will be added/kept. |
| **No Data Loss** | Done | Secondary contact marked `status = 'merged'`. All data moved to Master. |
| **Merge Audit Trail** | Done | `merged_contacts` table with JSON log of emails, phones, custom fields. |
| **Extensible Design** | Done | JSON in `merged_data`, separate tables, clean MVC. |
| **Clean Code & Best Practices** | Done | Transactions, validation, relationships, seeders. |

---

## Database Design

```text
contacts
├── id, name, email, phone, gender, profile_image, additional_file, status

custom_fields
├── id, name, type, options (json)

contact_custom_values
├── id, contact_id, custom_field_id, value

merged_contacts
├── id, master_contact_id, merged_contact_id, merged_data (json), merged_at
