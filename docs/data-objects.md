# Data Objects & Enums

## Job DataObject

The `Job` object provides a type-safe representation of a Jenkins job.

### Properties
- `name`: (string) The job name.
- `fullPath`: (string) Hierarchical path (e.g., `folder/job`).
- `url`: (string) Jenkins absolute URL.
- `type`: (?JobType) The type of job.
- `color`: (?string) Status color (e.g., `blue`, `red`, `anime`).
- `description`: (?string) Job description.

### Methods
- `toArray()`: Returns an associative array of the object's properties.

---

## JobType Enum

The `JobType` enum classifies Jenkins items based on their API class name.

### Cases
- `FOLDER`
- `WORKFLOW_JOB` (Pipeline)
- `WORKFLOW_MULTI_BRANCH_PROJECT`
- `FREE_STYLE_PROJECT`
- `ORGANIZATION_FOLDER`
- ...and more.

### Helpers
- `isContainer()`: Returns `true` if the type can contain other jobs.
- `isPureFolder()`: Returns `true` if the type is a pure organizational folder.
