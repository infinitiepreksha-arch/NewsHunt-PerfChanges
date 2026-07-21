# Implementation Plan: Update AGENTS.md with Living Implementation Plan & Recursive Feedback Protocol

This plan codifies the requirement that all planning revisions and user feedback during testing MUST be updated inside the **same** `implementation_plan.md` file continuously (living document), rather than creating separate plan files, until the user is 100% satisfied. Once fully satisfied, session files (`implementation_plan.md`, `task.md`, `walkthrough.md`) are archived into `.agents/features/YYYY-MM-DD_<feature_name>/`.

## Summary of What We Are Achieving
1. **Single Living Plan File (`implementation_plan.md`):** Throughout the entire lifecycle of a feature, from initial brainstorming to final testing approval, all plan updates, feedback adjustments, and revision steps are maintained inside a single `implementation_plan.md` file.
2. **Recursive Plan-Execute-Test Loop:** If testing yields feedback or issues, the agent updates the existing `implementation_plan.md` in place, waits for user approval, applies code edits, and re-presents for testing recursively until full satisfaction is reached.
3. **Session Archiving:** Once the user is fully satisfied after testing, `task.md` and `walkthrough.md` are finalized, and all 3 session files are archived together under `.agents/features/YYYY-MM-DD_<feature_name>/`.

---

## User Review Required

> [!IMPORTANT]
> This update formally codifies the **Single Living Document** rule across Phase 2, Phase 6, Phase 7, and Rule 11 in `.agents/AGENTS.md`.

---

## Proposed Changes

### `.agents/AGENTS.md`

#### [MODIFY] [AGENTS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/AGENTS.md)
- **Workflow Diagram Update:** Add the recursive loop from Phase 6 back to Phase 2 (updating `implementation_plan.md` in place).
- **Phase 2 & 3 Guideline:** Add explicit **Living Document Rule** specifying that feedback/revisions MUST update the same `implementation_plan.md` file.
- **Phase 5 & 6 Guideline:** Add explicit **Recursive Feedback Loop** guideline for iterating with the user until 100% satisfaction.
- **Phase 7 Guideline:** Clarify that feature folder archiving (`.agents/features/YYYY-MM-DD_<feature_name>/`) occurs only after full user satisfaction.
- **Rule 11:** Add Rule 11 under Mandatory System Rules.

### `.agents/AGENTS_CHANGES_LOG.md`

#### [MODIFY] [AGENTS_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/AGENTS_CHANGES_LOG.md)
- Log entry #7 for the update of `.agents/AGENTS.md` with the Living Implementation Plan and Recursive Feedback Loop rule.

---

## Verification Plan

### Manual Verification
- Review updated [.agents/AGENTS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/AGENTS.md) to ensure formatting and wording strictly reflect the requested protocol.
- Verify [.agents/AGENTS_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/AGENTS_CHANGES_LOG.md) master table count is updated accurately.
