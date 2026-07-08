# Google OAuth Setup — ShopGram

Login "Sign in with Google" kaj korar jonno Google Cloud Console theke OAuth client banate hobe, tarpor `.env` e boshate hobe.

## Step 1: Google Cloud Console e jao

1. Browser e jao: https://console.cloud.google.com
2. Gmail diye login koro (jekono gmail, jeta diye console access ache).

## Step 2: New Project banao (na thakle)

1. Top bar e project dropdown (naam er pashe) click koro.
2. **New Project** click koro.
3. Project name dao (e.g. `ShopGram`).
4. **Create** click koro. Kisukhon wait koro project ready hote.
5. Notun project select kore neo (top dropdown theke).

## Step 3: OAuth Consent Screen setup

1. Left sidebar → **APIs & Services** → **OAuth consent screen**.
2. User type: **External** select koro → **Create**.
3. App information:
   - App name: `ShopGram` (ba jekono)
   - User support email: nijer gmail
   - Developer contact email: nijer gmail
4. **Save and Continue** (Scopes step skip kora jai — **Save and Continue**).
5. **Test users** step e nijer gmail(s) add koro (app "Testing" mode e thakle sudhu eider login korte dibe).
6. **Save and Continue** → **Back to Dashboard**.

## Step 4: OAuth Client ID create koro

1. Left sidebar → **APIs & Services** → **Credentials**.
2. Top e **+ Create Credentials** → **OAuth client ID**.
3. Application type: **Web application**.
4. Name: `ShopGram Web` (ba jekono).
5. **Authorized redirect URIs** → **+ Add URI**:
   ```
   http://localhost:8000/auth/google/callback
   ```
   (Production e deploy korle production domain er URI o add korte hobe, e.g. `https://yourdomain.com/auth/google/callback`)
6. **Create** click koro.

## Step 5: Client ID & Secret copy koro

Popup e dekhabe:
- **Client ID** — `xxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com` format e
- **Client Secret** — `GOCSPX-xxxxxxxxxxxxxxxxxxxx` format e

⚠️ **Client Secret ekbar e dekhabe, popup close korle r dekha jabe na.** Copy kore rakho, na hoy **Download JSON** kore rakho.

(Pore access lagle: Credentials page e client name e click korle Client ID abar dekha jai, kintu Secret regenerate korte hobe jodi harai.)

## Step 6: `.env` file e boshao

Project root er `.env` file e ei 3 ta line update koro:

```env
GOOGLE_CLIENT_ID=<tomar Client ID>
GOOGLE_CLIENT_SECRET=<tomar Client Secret>
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**Real value kokhono `.md` file ba git e commit korba na — sudhu `.env` e rakhbe (eta gitignore e ache).**

## Step 7: Config cache clear koro

```bash
php artisan config:clear
```

## Step 8: Test koro

1. App run koro (`php artisan serve` — port 8000 match hote hobe redirect URI er sathe).
2. Login page e "Sign in with Google" click koro.
3. Google consent screen ashle, test user hishebe add kora gmail diye login try koro.

## Common Errors

| Error | Karon | Fix |
|---|---|---|
| `Error 401: invalid_client` | Client ID/Secret bhul ba placeholder | `.env` e shothik value ache kina check koro |
| `redirect_uri_mismatch` | Console e add kora URI ar app er redirect URI match korche na | Console e exact same URI add koro (http/https, port, trailing slash sob match hote hobe) |
| `Access blocked: App not verified` | Consent screen "Testing" mode e, user test list e nai | OAuth consent screen → Test users e email add koro |
| App production e publish korte gele | Consent screen "In production" korte hobe | OAuth consent screen → **Publish App** (Google review lagte pare sensitive scope thakle) |
