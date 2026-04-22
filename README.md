# matreshka

WordPress project assets for `news.matrezhka.ru`.

## Project structure

- `local-theme/` - active custom theme (`wp-content/themes/local-theme`)
- `mu-plugins/` - must-use plugins (`wp-content/mu-plugins`)
- `.github/workflows/deploy.yml` - auto-deploy to production on push to `main`

## Local run

```bash
docker compose -f docker-compose.local.yml up -d
```

Local site: `http://localhost:8090`

## Auto-deploy (GitHub Actions)

Workflow deploys theme + MU plugins to server:

- target host: `lamber6o.beget.tech`
- WordPress path: `/home/l/lamber6o/news.matrezhka.ru/public_html/wp-content`

### 1) Add repository secrets

In GitHub repository: `Settings -> Secrets and variables -> Actions -> New repository secret`

Create these secrets:

- `DEPLOY_HOST` = `lamber6o.beget.tech`
- `DEPLOY_PORT` = `22`
- `DEPLOY_USER` = `lamber6o`
- `DEPLOY_WP_CONTENT` = `/home/l/lamber6o/news.matrezhka.ru/public_html/wp-content`
- `DEPLOY_SSH_KEY` = private SSH key (full content)

### 2) Create deploy SSH key

On your local machine:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/matre_deploy -C "github-actions-matreshka" -N ""
```

Public key: `~/.ssh/matre_deploy.pub`
Private key: `~/.ssh/matre_deploy`

### 3) Add public key to server

```bash
ssh lamber6o@lamber6o.beget.tech
mkdir -p ~/.ssh
chmod 700 ~/.ssh
cat >> ~/.ssh/authorized_keys
# paste content of ~/.ssh/matre_deploy.pub, then Ctrl+D
chmod 600 ~/.ssh/authorized_keys
```

### 4) Put private key in GitHub secret

Copy full content of `~/.ssh/matre_deploy` into `DEPLOY_SSH_KEY`.

### 5) Push to main

Any push touching `local-theme/**` or `mu-plugins/**` triggers deploy.

You can also run it manually: `Actions -> Deploy WordPress Assets -> Run workflow`.

## First push to GitHub (new repo)

```bash
cd /Users/denis/matre
git init
git add .
git commit -m "Initial project setup with deploy workflow"
git branch -M main
git remote add origin https://github.com/PerfZero/matreshka.git
git push -u origin main
```
