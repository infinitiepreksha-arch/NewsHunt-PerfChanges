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

### Phase 7: Automatic History Logging & File Tracking (MANDATORY)
* **Rule:** At the end of every task execution, the agent MUST automatically update the history and tracking files without requiring prompts from the user.
* **Documentation Architecture:**
  1. **Global Project History ([PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md)):** Append a master summary of the task, logic changes, files modified, and verification results.
  2. **Codebase File Index ([FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md)):** Append entry listing modified application files and update the Master Go-To Index Table count.
  3. **Specialized Deep-Dive Logs (inside `.agents/`):**
     * **Performance Optimizations:** If the task involves speed, caching, queries, memory, or Core Web Vitals, append to [.agents/performance_optimization_history.md](file:///.agents/performance_optimization_history.md) using the required format (*Root Cause, Solution & Rationale, Files Modified, Code Comparison Diffs, Impact & Scalability*).
     * **New Features & Functional Updates:** If the task involves a new feature, UI enhancement, or non-performance change, append to [.agents/feature_development_history.md](file:///.agents/feature_development_history.md) using the required format (*Feature Need, Solution & Rationale, Files Modified, Code Comparison Diffs, Impact & Scalability*).
     * **Agent Configuration Changes:** If the task modifies `.agents/` files, append to [.agents/AGENTS_CHANGES_LOG.md](file:///.agents/AGENTS_CHANGES_LOG.md).
  4. **Turn Summary:** Report task changed file count and total cumulative unique file count in the final turn response.

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
9. **Browser & Autonomous Subagent Authorization Rule:** NEVER launch browser subagents (`browser_subagent`) or autonomous subagent modes without explicit user permission. Always ask the user first, explain why agentic mode is necessary, and provide clear step-by-step instructions on how the user can provide the required information directly to move forward without launching subagents. Only launch a subagent if the user is unable to provide the details and explicitly approves starting agentic mode.



