# NewsHunt Workspace Agent Protocol & Operating Rules (`.agents/AGENTS.md`)

This document defines the strict, non-negotiable operational workflow that every AI coding agent working on this repository **MUST ALWAYS FOLLOW**.

---

## 1. Core Workflow Phases

```
+-------------------------------------------------------------------------------+
| PHASE 1: Brainstorming & Discussion                                          |
| Discuss options, pros/cons, design choices, & align with the user             |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 2: Implementation Plan                                                  |
| Create implementation plan document & present it for review                   |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 3: User Plan Approval                                                   |
| STOP & WAIT for explicit user approval before making any code changes          |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 4: Feature Branch Creation                                             |
| Create a new feature branch (git checkout -b feature/<feature-name>)          |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 5: Code Execution & Self-Verification                                   |
| Implement minimal code changes & test across web, admin, and mobile APIs      |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 6: Code Review & User Feedback                                          |
| Present changed files & diffs for user review. WAIT for user ok to proceed    |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 7: History Logging & File Tracking                                      |
| Update PROJECT_HISTORY.md and FILE_CHANGES_LOG.md                             |
+-------------------------------------------------------------------------------+
                                      │
                                      ▼
+-------------------------------------------------------------------------------+
| PHASE 8: Git Commit & Push Authorization                                      |
| ASK FOR PERMISSION BEFORE COMMITTING OR PUSHING. NEVER COMMIT WITHOUT OK     |
+-------------------------------------------------------------------------------+
```

---

## 2. Detailed Phase Guidelines

### Phase 1: Detailed Brainstorming & Discussion
* Before taking any action or writing code, initiate a brainstorming session.
* **Pragmatic Analysis (No Blind "Textbook" Best Practices):** Do not blindly apply generic or textbook "best practices." Evaluate solutions based on *this specific project's scale, scenario, and real-world behavior* (e.g. a simple linear search or in-memory array operation might be far superior to an over-engineered index or complex cache for small datasets).
* Explore all edge cases and scenarios. If stuck, uncertain, or weighing trade-offs, ask the user directly—work hand-in-hand to find the best fit for NewsHunt.
* Exchange feedback until fully aligned on the best strategy before drafting the plan.

### Phase 2 & 3: Implementation Plan & Explicit Approval
* Draft a detailed implementation plan document (`implementation_plan.md`).
* **STRICT GUARD:** **STOP AND WAIT for explicit user approval** before making any code edits or running modifying scripts.

### Phase 4: Feature Branching Protocol
* **Rule:** **ALWAYS create a new git branch** for any new feature, bug fix, or optimization (e.g. `git checkout -b feature/<feature-name>`).
* **Why:** Keeps `main` stable, makes rollbacks trivial, and prevents git merge conflicts.

### Phase 5 & 6: Minimal Code Changes & Code Review
* Implement minimal, production-ready changes following existing Laravel patterns, `FileService` WebP rules, and `SelectsFields` query projections.
* Present the changed files, diff snippets, and testing results to the user.
* **STRICT GUARD:** **WAIT for the user to review the code changes and give permission to proceed.**

### Phase 7: History & File Tracking
* Append execution details to [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md).
* Append the entry and update the master index table in [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md) (for application code files).
* Track internal `.agents/` modifications separately in [.agents/AGENTS_CHANGES_LOG.md](file:///.agents/AGENTS_CHANGES_LOG.md).
* In turn summaries, state:
  * 📄 **Task changed files count:** X files
  * 📊 **Cumulative unique files count:** Y files

### Phase 8: Git Commit & Push Authorization
* **STRICT GUARD:** **NEVER COMMIT OR PUSH WITHOUT EXPLICIT USER PERMISSION.**
* Ask the user: *"Would you like me to commit these changes to git on branch `feature/<feature-name>`?"*
* Only run `git commit` or `git push` after receiving unambiguous user authorization.

---

## 3. Mandatory Coding & System Safety Rules

1. **Mobile REST API Safety (`/api/v1/`):** Never break existing response key names or Sanctum token authorization.
2. **Cross-Device UI Responsiveness:** Ensure Web UI elements maintain responsiveness across Mobile, Tablet, and Desktop viewports. Preserve explicit element dimensions to prevent Cumulative Layout Shift (CLS).
3. **Request-Scoped Cache Protection:** Use `request()->attributes` instead of PHP class `static` properties inside Providers/Controllers.
4. **Model Hydration Blowup Prevention:** Use `take(5)` limits or `DB::table()` queries instead of loading massive Eloquent collections in loops.
5. **WebP Compression Pipeline:** Pass uploaded images through `FileService::resizeAndCompressUpload()`.
6. **PHP 8.1 Null-Safety:** Null-coalesce variables passed to native string functions (`html_entity_decode($str ?? '')`).
7. **.agents Folder Branching Rule:** All changes made to the `.agents/` directory (guidelines, skills, knowledge base, agent logs) MUST ALWAYS be committed and pushed to the `main` branch so that all future feature branches automatically inherit the latest metadata and rules.
8. **Pragmatic & Context-Driven Problem Solving:** Never jump to conclusions or blindly implement textbook patterns. Always analyze solutions through the lens of *this specific project, scenario, data scale, and CodeCanyon maintainability*. If uncertain or facing trade-offs, ask the user to collaborate hand-in-hand.


