# Convo CRM

## Product Requirements Specification – v0.2 (MVP)

### 1. Purpose
1. Define a modern, web-based CRM focused on conversations as the core of closing sales.
2. Provide an interface visually and structurally similar to Salesforce Lightning (card-based, clean, neutral).
3. Support multiple salespeople logging in concurrently, each managing their own/op shared pipeline.
4. Deliver a minimal but solid core that can be extended iteratively.

### 2. Naming
1. **Product name:** Convo (C-O-N-V-O).
2. **Extended name:** Convo CRM for documentation, marketing copy, and technical references where clarity is needed.
3. **System identifier / namespace:** `convo`.

### 3. Platform & Technology Stack
#### 3.1 Application Type
- Browser-based web application.
- Responsive design (desktop-first, usable on tablet).

#### 3.2 Frontend
- HTML5, minimal JavaScript (framework TBD: can start with vanilla JS or later adopt a framework).
- Tailwind CSS for styling and layout.
- **Aesthetic:**
  - Light/white neutral background.
  - Light grey borders and dividers.
  - Card-based layouts inspired by Salesforce Lightning.
  - Clear typographic hierarchy and spacing.

#### 3.3 Backend
- Language: PHP (PHP 8.x recommended).
- Framework: initially optional; can start with structured plain PHP, or later adopt a micro-framework (e.g., Slim/Lumen) if needed.

#### 3.4 Database
- SQLite (single-file DB for simplicity and easy deployment).
- One SQLite database per Convo instance (single-tenant instance with multiple users).

#### 3.5 Hosting / Deployment (assumed)
- Standard PHP-capable web hosting or VPS.
- File-based SQLite storage with regular backups.

### 4. Security & Authentication
#### 4.1 User Accounts
Core fields:
- `id` (integer, PK)
- `username` (unique)
- `password_hash` (secure hashing, e.g. `password_hash()` in PHP)
- `display_name`
- `email` (optional in MVP)
- `role` (e.g. user, admin – simple role model)

#### 4.2 Authentication
- Simple username + password login.
- Password storage: hashed + salted via PHP standard functions.
- Session-based authentication:
  - PHP sessions or session tokens stored in HTTP-only cookies.
- Login required for all CRM pages (except login/register).

#### 4.3 Authorization
- **MVP:**
  - All authenticated users can view and edit all data (team-based model).
  - Admin role: can manage users (create, deactivate, reset password).
- **Later:** record-level permissions, ownership, teams.

#### 4.4 Security Considerations
- Server-side validation of all requests.
- CSRF protection on forms (CSRF tokens).
- Basic rate-limiting on login to reduce brute-force attempts.
- Input sanitization to avoid SQL injection (prepared statements) and XSS.

### 5. Data Model (MVP)
#### 5.1 Core Entities
**User**
- Fields as per 4.1.

**Account**
- `id`
- `name`
- `industry` (optional)
- `website` (optional)
- `phone` (optional)
- `billing_address` (optional text)
- `created_at`
- `updated_at`
- `owner_user_id` (optional, reference User)

**Contact**
- `id`
- `account_id` (FK to Account)
- `first_name`
- `last_name`
- `email` (optional)
- `phone` (optional)
- `role` (e.g. decision maker, influencer)
- `created_at`
- `updated_at`

**Opportunity**
- `id`
- `name`
- `account_id` (FK to Account)
- `owner_user_id` (FK to User)
- `stage_id` (FK to Stage)
- `amount` (numeric)
- `close_date` (date)
- `probability` (optional, numeric)
- `next_step` (short text)
- `description` (long text, optional)
- `created_at`
- `updated_at`

**Stage**
- `id`
- `name` (e.g. "Prospecting", "Qualification", "Proposal", "Negotiation", "Closed Won", "Closed Lost")
- `order_index` (for Kanban column order)
- `is_closed` (boolean)
- `is_won` (boolean)

**Activity (conversation log / tasks / notes tied to opportunities)**
- `id`
- `opportunity_id` (FK to Opportunity)
- `user_id` (FK to User)
- `type` (call, meeting, email, note, task)
- `subject`
- `details` (text)
- `due_date` (for tasks, optional)
- `completed_at` (for tasks, optional)
- `created_at`

(Attachments, automation, and advanced reporting are future phases.)

### 6. Functional Requirements – MVP
#### 6.1 Authentication & User Management
- **Login page:**
  - Username + password form.
  - Basic error feedback on wrong credentials.
- **Logout functionality.**
- **Admin-only:**
  - Create new user, set username/password.
  - Toggle user active/inactive.
  - Reset user password.

#### 6.2 Global Layout & Navigation
- **Top navigation bar:**
  - App logo/name: "Convo".
  - Main sections: "Accounts", "Opportunities", "Pipeline" (Kanban), "(optional) Dashboard".
  - User menu (profile, logout).
- **Optional global search:**
  - Simple keyword search across Accounts/Opportunities (can be deferred if needed).

#### 6.3 Accounts
- **List view:**
  - Table/list of accounts with key fields (Name, Industry, #Open Opportunities).
  - Filters: search by name.
- **Create / edit account:**
  - Simple form, inline Tailwind styling.
- **Account detail page:**
  - Header: account name, core info.
  - Sections:
    - Related opportunities list.
    - Related contacts list.
  - Actions: create new Opportunity or Contact linked to this Account.

#### 6.4 Contacts (MVP-level)
- **Create / edit Contact from:**
  - Account page.
  - Opportunity page (with account pre-selected).
- **Display on:**
  - Account detail (list of contacts).
  - Opportunity detail (contact roles / key contacts).

#### 6.5 Opportunities – List View
- Tabular or card list of opportunities:
  - Columns: Name, Account, Stage, Amount, Close Date, Owner.
- **Filters:**
  - By stage.
  - By owner.
- **Sort by** close date, amount, or stage.

#### 6.6 Opportunity Detail Page
- **Layout inspired by Salesforce Lightning:**
  - **Header:**
    - Opportunity name.
    - Stage "path" component (horizontal stage bar showing current stage).
    - Key fields: Account, Amount, Close Date, Owner.
  - **Main body (three-column style on desktop):**
    - **Left column:**
      - Core fields (editable inline or via "Edit" action).
      - Stage selector (can change stage here as well).
    - **Center column:**
      - Activity & Next Steps focus.
      - **Activity timeline:**
        - List of activities sorted newest-first.
        - Icon/label by activity type.
        - Quick add activity form (subject, type, details).
      - **Next step:**
        - Prominent field showing "Next step" summary.
        - Optional due date.
    - **Right column:**
      - Related contacts for this opportunity.
      - Key contact marker.
      - (Optionally) simple notes widget.
  - **Actions:**
    - Edit opportunity.
    - Add activity.
    - Add contact (link existing account contact or create new).

#### 6.7 Pipeline / Kanban View
- **Board layout with one column per Stage.**
- Each column:
  - Header with stage name and aggregated amount (sum of opportunity amounts in that column).
  - List of opportunity cards:
    - Card fields: Opportunity name, Account, Amount, Close date, Owner.
- **Behavior:**
  - Drag-and-drop opportunity cards between stages:
    - On drop, update `stage_id` in database.
- **Filters:**
  - By owner (My opportunities vs All).
- **Basic performance requirement:** drag-drop should feel instantaneous; backend update via AJAX or similar.

#### 6.8 Dashboard (Optional MVP, can be rudimentary)
- Simple metrics on the right side or on a small "Dashboard" view:
  - Total pipeline amount.
  - Amount per stage.
  - Number of open opportunities.
- Charts can initially be simple numeric summaries; graphical charts are a later enhancement.

### 7. Non-Functional Requirements
#### 7.1 Performance
- Reasonable response times on common shared hosting:
  - Page loads under typical conditions: target < 1s backend processing time for standard queries.
- Efficient SQLite queries with indexes on `stage_id`, `owner_user_id`, `account_id`.

#### 7.2 Reliability
- SQLite database integrity:
  - Use transactions for create/update/delete.
- **Backup strategy:** documented file-level DB backup process.

#### 7.3 Maintainability
- **Code organization:**
  - Clear separation of concerns: routes/controllers, models, views/templates, utilities.
- Single configuration file for DB path, app settings.

#### 7.4 UX Guidelines
- Minimal clutter; emphasize:
  - Current stage.
  - Activities.
  - Next steps.
- Consistent Tailwind-based design tokens (spacing, font sizes, colors).
- Keyboard-friendly forms if possible.

### 8. Future Enhancements (Out of Scope for MVP)
- Email integration (log emails as activities).
- Automated workflows (e.g., stage-based tasks).
- Advanced reporting and custom dashboards.
- Multi-tenant SaaS (multiple companies in one instance).
- Mobile-optimized interaction patterns.
- API for integrations.
- File attachments and document management.
- Advanced role-based permissions and teams.

### Next Steps
If you want, the next step can be:
- Turn this into a GitHub `README.md` + `docs/spec.md`.
- Or design the initial SQLite schema (`CREATE TABLE` statements) and basic PHP page structure.
