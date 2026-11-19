# Medicare

A small PHP web app for patient self-service appointment booking, backed by
Supabase.

Medicare lets a patient register and then book or review appointments from a
single schedule page, behind a cookie-based session. It is a server-rendered
app: plain PHP templates styled with hand-written CSS, routed by FastRoute,
with Supabase's PostgREST API as the data store.

- **Patient registration.** Sign-up validates name, email, password, phone and
  address, hashes passwords with bcrypt and starts a cookie-based session.
- **Appointment booking.** A schedule page books date, time and provider and
  lists the patient's upcoming appointments.
- **Supabase storage.** Patient and appointment records live in Supabase and
  are read and written through its REST API.
- **Minimal dependencies.** One Composer package (`nikic/fast-route`), one
  front controller, no framework and no JavaScript build step.

## Run it

You need PHP 7.3 or newer with the cURL extension, Composer, and a Supabase
project with `patients` and `appointments` tables.

```bash
git clone https://github.com/jayptl-me/medicare.git
cd medicare
composer install

export SUPABASE_URL="https://<your-project>.supabase.co"
export SUPABASE_KEY="<your-service-or-anon-key>"

php -S localhost:8000 -t public
```

Then open http://localhost:8000/register to create a patient account.

## License

Licensed under the Apache License, Version 2.0. See `LICENSE` and `NOTICE`.
