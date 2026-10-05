# Automatic Git Commit & Push Mandate

## Mandatory Rule for AI Assistant
Whenever you modify, create, or delete any files in this project:
1. **Never wait for the user to ask for a commit**: You MUST automatically stage and commit the changes as part of completing the user's task.
2. **Standard Conventional Commits**: Use descriptive commit messages following conventional commit style (`feat:`, `fix:`, `style:`, `refactor:`, `docs:`, `chore:`).
3. **Keep Remote Synced**: Push the commits to `origin main` to keep GitHub synchronized.
4. **Security Priority**: Never stage or commit `.env`, `.env.*`, SSL keys, logs, passwords, or financial spreadsheets (`.xlsx`). Respect `.gitignore`.
5. **Clear Feedback**: Briefly summarize to the user what was committed and confirmed pushed to GitHub.
