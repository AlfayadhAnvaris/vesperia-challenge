# Technical questions

## 1. How long did you spend on the coding test? What would you add if you had more time?

I completed the implementation iteratively across multiple working sessions and did not keep a reliable hour-by-hour time log, so I do not want to invent an exact duration. The delivered work includes the Laravel JSON-feed importer, JSON-backed form and submission storage, API endpoints, a schema-driven frontend form, role-specific dashboards, database migrations, demo data seeders, and automated feature tests.

With more time, I would:

- Add an import command that supports larger feeds with bounded-memory streaming or chunked processing, transactional upserts, and a clear import summary.
- Add integration tests against the production database engine (MySQL/MariaDB), in addition to the isolated SQLite test suite.
- Add API resources and explicit API versioning, plus tests for pagination limits, malformed feeds, and concurrent/retried imports.
- Add operational visibility for import failures and submission trends, along with accessibility and browser-based end-to-end coverage.

## 2. How would you track down a performance issue in production? Have you ever had to do this?

I would first establish the impact and scope using production metrics: request latency percentiles (especially p95/p99), throughput, error rate, CPU, memory, database connections, and queue depth. I would correlate the regression with deployments and inspect distributed traces and structured logs to identify whether time is spent in application code, database queries, external services, or queue processing.

For a database bottleneck, I would capture the slow query and its execution plan, check rows examined, indexes, lock waits, and query frequency, then reproduce it with representative data. For application or memory bottlenecks, I would profile the affected endpoint and inspect allocation/query patterns. I would test a targeted change under a representative load test, deploy it gradually, and compare the same metrics before and after, with rollback criteria in place.

I have not been given production access or production incident evidence for this test project, so I cannot claim to have diagnosed a production issue for this application. I would be transparent about that in an interview and describe any relevant production experience separately.
