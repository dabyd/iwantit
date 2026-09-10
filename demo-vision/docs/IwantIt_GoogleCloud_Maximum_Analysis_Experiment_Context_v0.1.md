# IwantIt — Google Cloud Maximum Analysis Experiment
## Context & working plan for David
### v0.1 — September 2026

## 1. Purpose

This document captures the context and conclusions for the first IwantIt technical experiment on Google Cloud.

This is **not a production implementation of IwantIt's Analysis product**.

The objective is to test the current state of AI-assisted audiovisual analysis as aggressively as possible:

> **Take one complete audiovisual episode, use the maximum relevant capabilities available in Google Cloud, extract as much information as technically possible, and compare the AI-generated analysis against the manual analysis already produced by IwantIt.**

We want to understand:

- what AI can already detect reliably;
- what AI can detect but requires human validation;
- what AI misses;
- what AI discovers that our manual analysis missed;
- what information can be extracted beyond the current IwantIt Guide;
- how much human work can potentially be removed;
- whether the analysis model defined by IwantIt is technically achievable;
- where the real technical limitations are today.

For this first experiment, **we optimize for maximum information and recall, not production cost or architectural elegance**.

---

## 2. Test asset

### Video

**Emily in Paris — Season 3, Episode 2**

The episode is currently available in AWS as an MP4 and can be moved/copied to Google Cloud Storage.

### Existing AI pilot

The previous AI pilot should be considered **obsolete and non-functional for this experiment**.

We are not building on top of it.

### Existing analysis

We already have a **manual analysis of the episode**.

This is our reference baseline for evaluating the AI output.

The manual analysis is not assumed to be perfect ground truth. It is the best existing human analysis and therefore the baseline against which we compare the new automated analysis.

---

## 3. What we are trying to prove

The central question is:

> **How far can current AI technology go in analysing a complete audiovisual work?**

More specifically:

### Capability

Can the information defined in the IwantIt analysis model actually be obtained from the audiovisual asset?

### Quality

Is the AI output:

- significantly below the manual analysis;
- approximately equivalent;
- better in some areas;
- better overall?

### Scalability

Manual analysis currently takes days per project.

We need to understand whether AI can reduce this effort sufficiently to make analysis scalable.

### Discovery

We also want to know whether AI can identify things that our current guide or manual methodology does not explicitly ask for.

Therefore:

> **The IwantIt Guide is the minimum target, not the maximum target of this experiment.**

---

## 4. Maximum Intelligence principle

We should not constrain the experiment to only the capabilities strictly necessary for the current product.

We have Google Cloud credits and this is a feasibility experiment.

Therefore:

> **Use everything available in Google Cloud that can reasonably contribute to audiovisual understanding.**

If two Google services can perform similar tasks, consider testing both.

If a capability is not currently contemplated in the Guide but could reveal useful information, test it.

If a capability proves useless, that is still a valid result.

The goal is to discover the ceiling.

---

## 5. Conceptual experiment architecture

The first version should remain simple. We do not need production-grade distributed infrastructure.

```text
                         EMILY IN PARIS S03E02
                                  |
                                  v
                         Google Cloud Storage
                                  |
                                  v
                       Maximum Analysis Pass
                                  |
        +-------------------------+-------------------------+
        |                         |                         |
        v                         v                         v
 Video Intelligence            Vision                    Gemini
        |                         |                         |
        +-------------------------+-------------------------+
                                  |
                           Speech-to-Text
                                  |
                         Multimodal Embeddings
                                  |
                                  v
                          RAW AI RESULTS
                                  |
                                  v
                       IwantIt Normalization
                                  |
                                  v
                       AI Analysis Dataset
                                  |
                     +------------+------------+
                     |                         |
                     v                         v
               Manual Analysis          AI vs Manual
```

Critical principle:

**Keep the raw provider outputs.**

Do not immediately transform everything into the final IwantIt model and discard what the providers actually returned.

---

## 6. Google Cloud capabilities to test

### 6.1 Video Intelligence

Use the maximum relevant video-analysis capabilities available.

Potential areas:

- shot detection / scene boundaries;
- object detection;
- object tracking;
- logo recognition;
- text detection;
- person detection;
- face detection;
- video labels;
- speech transcription;
- explicit-content analysis;
- other relevant video features available in the current environment.

For every result, preserve as much information as the provider exposes, including where applicable:

- timestamp;
- start/end time;
- duration;
- bounding box / geometry;
- track;
- label;
- entity;
- confidence;
- provider metadata.

We want to know not only **what** exists, but where, when, for how long, and what additional information the service exposes.

---

### 6.2 Cloud Vision

Use Vision as a complementary frame/image-analysis layer.

Relevant capabilities to investigate:

- OCR;
- logo detection;
- object localization;
- image labels;
- face detection;
- landmark detection;
- SafeSearch / content signals;
- image properties;
- web detection / matching where applicable;
- other useful image-analysis capabilities exposed by the service.

The objective is to compare frame-level analysis with video-level analysis.

For example:

```text
Video Intelligence -> Logo = X
Cloud Vision       -> Logo = X
Gemini             -> Brand = X
Manual             -> Brand = X
```

Agreement is useful. Disagreement is also useful.

---

### 6.3 Speech-to-Text

Extract the full available speech information.

Potential outputs:

- transcript;
- timestamps;
- word-level timing where available;
- speakers / diarization where available;
- confidence information;
- language-related information.

The transcript will subsequently become an input to semantic analysis and entity extraction.

---

### 6.4 Gemini / Vertex AI

Gemini should be treated as the main semantic/multimodal reasoning layer.

Do **not** rely on one generic request such as "Analyze this video."

Run several specialized analysis passes.

Potential passes:

#### Global understanding
What happens throughout the episode?

#### Scene understanding
For each meaningful scene:

- setting;
- location;
- people;
- objects;
- products;
- brands;
- visible text;
- activities;
- mood;
- narrative context;
- commercial context.

#### Object inventory
Identify as many visually meaningful objects as possible.

#### Product inventory
Identify recognizable consumer products.

#### Brand inventory
Identify visible and, where appropriate, audible brands.

#### Fashion inventory

Especially relevant for *Emily in Paris*:

- clothing;
- footwear;
- handbags;
- accessories;
- jewellery;
- watches;
- cosmetics;
- eyewear;
- fashion brands.

#### Food & beverage

- food;
- drinks;
- restaurants;
- packaging;
- brands.

#### Places

- cities;
- streets;
- buildings;
- landmarks;
- venues;
- recognizable interiors.

#### People

Identify people as visual entities, their appearances, activities and interactions.

#### Activities

What are people doing?

#### Relationships

Identify meaningful relationships such as:

- person ↔ person;
- person ↔ object;
- person ↔ brand;
- person ↔ product;
- person ↔ location;
- object ↔ object.

#### Context

Identify contextual characteristics of each scene.

#### Commercial signals

Identify elements that may have commercial relevance.

#### Clearance-like inventory

Identify recognizable elements that may later be relevant to clearance.

This is discovery, **not a legal conclusion**.

#### Audio/context

Extract relevant information from the audio and surrounding context.

---

## 7. Dense visual analysis

A single sampling rate should not define the experiment.

We should test dense frame analysis where useful, potentially:

- 1 FPS;
- higher FPS for selected analysis;
- targeted high-resolution frames;
- high-resolution crops.

The objective is to discover how much information is lost through sampling.

Example:

```text
Full frame
    |
    +-- Person crop
    +-- Product crop
    +-- Logo crop
    +-- Text crop
    +-- Object crop
```

This is especially important for:

- small logos;
- packaging;
- product models;
- text;
- fashion details;
- artwork;
- background objects.

For this experiment, prioritize discovering the maximum possible signal before optimizing cost.

---

## 8. Open discovery

We should not only ask:

> "Find the things that IwantIt already knows how to classify."

We should also ask:

> **"What else is interesting or identifiable in this audiovisual content?"**

Potential discoveries could include:

- artwork;
- books;
- magazines;
- apps;
- interfaces;
- specific car models;
- architecture;
- fashion items;
- packaging;
- instruments;
- cultural references;
- specific locations;
- websites or URLs visible on screen;
- secondary brands;
- secondary products;
- other entities not currently represented in the IwantIt Guide.

These discoveries should be preserved even if we do not yet know where they fit in the IwantIt domain model.

---

## 9. Casting / people

Casting should not be treated as a requirement to build proprietary facial recognition.

The intended workflow is:

```text
Video
  |
  v
Person detection / tracking
  |
  v
Person candidates
  |
  v
Cast information for the project
  |
  +--> IMDb / TMDB / other appropriate sources
  |
  v
Matching
  |
  v
Person -> Actor / Character
```

If the automated match is reliable, record it.

If not, test whether the system can produce useful candidate matches for human confirmation.

The key experiment is:

> **How much of the Person → Character mapping can be automated?**

---

## 10. Multimodal embeddings

Test embeddings as an additional intelligence layer.

Potential inputs:

- video segments;
- frames;
- scene descriptions;
- transcript;
- entities;
- product descriptions;
- brand descriptions;
- other normalized observations.

Potential uses:

- semantic similarity;
- grouping;
- deduplication;
- retrieval;
- matching;
- repeated appearances;
- similarity between visual and textual descriptions.

Embeddings are not the source of truth. They are another signal that can help discover relationships and perform matching.

---

## 11. Preserve RAW results

The experiment should maintain at least four conceptual layers:

```text
01_RAW
    Provider outputs exactly as returned

02_NORMALIZED
    Common IwantIt observation format

03_ANALYSIS
    Structured IwantIt analysis candidates

04_EVALUATION
    AI vs Manual comparison
```

This lets us return to the original provider output if a later normalization step loses or misinterprets information.

---

## 12. IwantIt normalization

After collecting raw results, create a common representation.

Example:

```json
{
  "type": "Brand",
  "label": "Nike",
  "start_ms": 105420,
  "end_ms": 109870,
  "geometry": {
    "x": 0.41,
    "y": 0.27,
    "width": 0.18,
    "height": 0.11
  },
  "confidence": 0.91,
  "provider": "google-video-intelligence",
  "model": "...",
  "evidence": "...",
  "source_reference": "..."
}
```

This is a **comparison representation**, not yet the production Core Content Inventory.

Provider provenance and evidence should remain attached to the result.

---

## 13. Do not automatically write AI results into Core

This experiment is not the point at which AI becomes IwantIt's source of truth.

The intended architecture remains:

```text
AI detects
    |
    v
Candidate
    |
    v
Human validation
    |
    v
IwantIt Core
```

For the experiment, however, we can analyse the candidates as a complete AI dataset before any production promotion.

---

## 14. AI vs Manual comparison

This is the central evaluation.

For every relevant category, compare:

- what the manual analysis found;
- what the AI found;
- what the AI missed;
- what the AI found that the manual analysis did not;
- where timing differs;
- where location differs;
- where identity differs;
- where semantic interpretation differs.

Classify differences as:

### MATCH
Manual and AI agree.

### AI MISS
Manual has the item; AI did not find it.

### AI FALSE POSITIVE
AI found something that is not actually correct.

### AI DISCOVERY
AI found something that the manual analysis did not record.

This must then be human-reviewed.

### TIMING DIFFERENCE
Same item, different start/end.

### SPATIAL DIFFERENCE
Same item, different bounding box/location.

### IDENTITY DIFFERENCE
Same visual entity but different identified brand/product/person.

### SEMANTIC DIFFERENCE
Both detect the underlying material but interpret its meaning/context differently.

---

## 15. Manual analysis is not assumed to be perfect

The manual analysis is our reference baseline, but it should not automatically be treated as absolute truth.

If AI finds something that manual analysis missed:

```text
AI discovery
     |
     v
Human review
     |
     +--> correct discovery
     |
     +--> AI false positive
```

One potential benefit of AI is therefore **higher completeness**, not merely replication of human analysis.

---

## 16. Metrics

### A. Capability

For each capability:

- PASS;
- PARTIAL;
- FAIL;
- NOT TESTED.

### B. Quality

Where applicable:

- precision;
- recall;
- coverage;
- completeness;
- false positives;
- false negatives;
- temporal accuracy;
- spatial accuracy;
- identity accuracy;
- semantic accuracy.

### C. Economics / scalability

Measure:

- AI processing time;
- manual analysis time;
- human review time;
- correction time;
- total human effort;
- estimated AI cost;
- human work eliminated.

The key business question is:

> **How much human work can AI remove while maintaining or improving analysis quality?**

---

## 17. Three benchmark questions

### Benchmark 1 — Capability

> What can Google Cloud AI technically extract from the episode?

### Benchmark 2 — Quality

> How does the resulting analysis compare with our existing manual analysis?

### Benchmark 3 — Economics

> How much faster and potentially more scalable is AI-assisted analysis?

---

## 18. Initial execution phases

### Phase 0 — Google Cloud setup

Before processing the episode:

- create/identify the dedicated Google Cloud project;
- verify billing;
- verify available credits;
- configure a budget/alert;
- enable required APIs;
- create Cloud Storage;
- establish authentication/permissions.

Do not build production infrastructure.

### Phase 1 — Smoke test

Use a **2–5 minute clip** from the episode.

Test:

- Cloud Storage;
- Video Intelligence;
- Vision;
- Speech-to-Text;
- Gemini.

Goal:

> **Prove that the complete experimental path works before processing the full episode.**

### Phase 2 — Full episode

Run the maximum relevant analysis on the complete episode.

Preserve every raw output.

### Phase 3 — Maximum capability pass

Run the broadest useful combination of:

- Video Intelligence;
- Vision;
- Speech-to-Text;
- Gemini / Vertex AI;
- multimodal embeddings;
- frame-level analysis;
- high-resolution targeted analysis;
- external metadata matching where appropriate.

### Phase 4 — Normalization

Convert provider-specific outputs into a common IwantIt analysis representation.

### Phase 5 — Evaluation

Compare AI against the manual analysis.

Classify differences and calculate metrics.

### Phase 6 — Conclusions

Produce a capability map:

```text
AUTOMATIC
ASSISTED
MANUAL
NOT FEASIBLE / NOT RELIABLE
```

Also identify:

- capabilities where AI is better;
- capabilities where manual remains better;
- capabilities where both are comparable;
- new discoveries outside the current Guide;
- major technical gaps;
- likely product opportunities.

---

## 19. What we are NOT optimizing yet

For this first experiment, do not prematurely optimize:

- production architecture;
- microservices;
- Kubernetes;
- CI/CD;
- production APIs;
- database architecture;
- cost per project;
- final model selection;
- final provider selection.

Those decisions come later.

The first experiment is about discovering the technical ceiling.

Priority:

> **MAXIMUM RECALL + MAXIMUM INFORMATION**

Then:

> precision → utility → automation → cost → production architecture.

---

## 20. Expected final deliverable

The experiment should produce a **Maximum Intelligence Report** covering:

### Episode coverage

- duration;
- scenes/shots;
- frames analysed;
- audio analysed.

### Visual inventory

- objects;
- products;
- brands;
- logos;
- text;
- people;
- faces;
- clothing;
- accessories;
- locations;
- landmarks;
- artwork;
- food;
- vehicles;
- other discovered categories.

### Temporal intelligence

- appearances;
- tracks;
- start/end;
- duration;
- frequency;
- prominence where available.

### Semantic intelligence

- activities;
- relationships;
- context;
- entities;
- narrative events;
- commercial signals.

### Audio intelligence

- transcript;
- speakers;
- languages;
- audio/context signals.

### Discovery

- unexpected entities;
- unexpected products;
- unexpected brands;
- unexpected visual concepts;
- information not currently represented in the Guide.

### AI vs Manual

- precision;
- recall;
- coverage;
- false positives;
- missed detections;
- AI discoveries;
- discrepancies.

### Human efficiency

- manual hours/days;
- AI processing time;
- review time;
- correction time;
- total human effort.

---

## 21. Key principle for David

The experiment should be approached as a **laboratory, not as a product implementation**.

The question is not:

> "How do we build the final IwantIt AI Analysis system?"

The question is:

> **"If we give today's best available Google Cloud AI stack a complete episode and ask it to understand as much as possible, what does it actually give us?"**

And then:

> **"How does that compare with the analysis that took us days to produce manually?"**

Only after answering those questions should we decide:

- which capabilities become part of the product;
- which providers we use;
- which models we build ourselves;
- which outputs require human validation;
- what becomes IwantIt proprietary IP;
- what the production architecture should look like.

---

## 22. Desired mindset

Do not be afraid of outputs that are messy, surprising, incomplete or outside the current model.

**That is the purpose of the experiment.**

If Google finds 10 things we did not contemplate, that is valuable.

If it misses half of our manual detections, that is valuable.

If it beats us on OCR but fails on product identity, that is valuable.

If Gemini discovers contextual relationships that were impossible to capture manually at scale, that is extremely valuable.

If some capability turns out not to be technically reliable, that is also a valuable conclusion.

We are trying to discover **where the technology really is in September 2026**, not confirm what we already believe.

---

## 23. Success condition

The experiment is successful if, at the end, we can look at the evidence and say with confidence:

> **We know what current Google Cloud AI can and cannot extract from a professional audiovisual work, how its output compares with IwantIt's manual analysis, what additional information it can discover, and how much human effort could potentially be removed.**

That evidence will guide the next stage of IwantIt's Analysis product and AI architecture.
