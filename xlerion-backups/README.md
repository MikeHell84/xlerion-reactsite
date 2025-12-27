xlerion-backups

Repository skeleton to store and sync backups of the main site.

Suggested usage:

```bash
cd xlerion-backups
git init
git remote add origin <your-backups-repo-url>
cp -r ../backup/* .
git add .
git commit -m "Initial backup"
git push -u origin main
```

See `sync_backups.sh` for a helper.
