# IwantIt — Context Bundle Specification v1.0

**Status:** ACTIVE FOR D0  
**Owner packet:** D0-000

## 1. Objective

Give an implementation/review agent the smallest exact normative context sufficient to execute one packet without relying on conversation history or guessing missing structure.

## 2. Required bundle structure

```text
<packet>-context/
  00-context-index.md
  constitution.md
  current-packet.md
  dependencies.md
  canonical-contracts.md
  relevant-decisions.md
  relevant-screen-contracts.md
  acceptance-tests.md
  prohibited-shortcuts.md
```

No file may silently replace a frozen source; each extracted rule should identify its source artifact/section or packet lineage.

## 3. File contracts

### `00-context-index.md`
Packet ID/title, risk, bundle version/date, registered source versions/hashes used, intended agent role, and completeness result.

### `constitution.md`
Compact copy/reference of the cross-cutting invariants relevant to all packets: source precedence, GREEN/AMBER/RED, STOP rules, Sources of Truth, temporal/authority/no-shortcut rules. It must not contain packet-specific invented semantics.

### `current-packet.md`
Objective, IN/OUT, required outputs, packet risk, prerequisites, acceptance, expected files/components where frozen, and timebox if applicable.

### `dependencies.md`
Only dependencies the packet may assume. Record GREEN status/evidence reference. A non-GREEN dependency cannot be treated as complete.

### `canonical-contracts.md`
Exact canonical entities, states, commands/queries/APIs/events, authority rules, temporal/rights implications and architecture baseline that the packet touches.

### `relevant-decisions.md`
Only Dxxx/FRZ/COR decisions materially relevant to this packet. Include outcome, not conversation history.

### `relevant-screen-contracts.md`
Written screen contracts touched. If none, state `NONE — this packet has no user-facing screen contract` rather than omitting the file.

### `acceptance-tests.md`
Canonical packet tests + D0 overlay tests relevant to the packet. Distinguish executable now vs tests that become executable after a later dependency exists.

### `prohibited-shortcuts.md`
Global shortcuts plus packet-specific traps. A reviewer uses this file as a negative checklist.

## 4. Bundle assembly rules

1. Start from the hash-pinned Specification Manifest.
2. Resolve packet and prerequisites from the active D0 Packet Register / Greenfield packet index.
3. Apply precedence before extracting clauses.
4. Include only contract-relevant excerpts/summaries; do not paraphrase away MUST/NEVER/exact-state semantics.
5. Every structural statement must trace to a registered source.
6. D0 may narrow breadth but never rewrite canonical truth.
7. Mark any unresolved conflict `STOP` in the context index.
8. A bundle is role-neutral by default; reviewers additionally receive diff/test evidence, not implementer reasoning.

## 5. Completeness gate

A bundle is `READY` only if a fresh agent can answer, without chat history:

- What exactly am I implementing?
- What is explicitly out of scope?
- What dependencies may I trust?
- Which Sources of Truth/aggregates/contracts can I touch?
- Which authority/temporal/rights rules apply?
- What acceptance evidence is required?
- What am I forbidden to invent or shortcut?
- What condition makes me STOP?

Any `UNKNOWN` structural answer → bundle `BLOCKED`.

## 6. Context-size policy

Prefer precision over volume. Full Guide/Greenfield may be available as fallback reference, but the normal agent prompt should load this bundle first and open broader sources only to resolve a cited ambiguity.
