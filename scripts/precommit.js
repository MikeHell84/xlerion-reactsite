const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const repoRoot = execSync('git rev-parse --show-toplevel').toString().trim();
const scssSrc = path.join(repoRoot, 'frontend', 'src', 'styles', 'xlerion.scss');
const cssOut = path.join(repoRoot, 'public', 'xlerion.css');

try {
    console.log('Compiling SCSS...');
    execSync(`npx sass "${scssSrc}" "${cssOut}" --no-source-map --style=compressed`, { stdio: 'inherit' });
} catch (err) {
    console.error('\nSCSS compilation failed. Fix errors before committing.');
    process.exit(1);
}


// Read compiled CSS and search for global white background patterns
const css = fs.readFileSync(cssOut, 'utf8');
const offenders = [];

// Only catch global selectors that set a white background on html/body.
// Allow component-level uses of #fff (e.g. cards, buttons) to pass.
const globalBgRegex = /(html\s*,\s*body|html|body)\s*\{[^}]*background\s*:\s*#(?:fff|ffffff)\b/i;
if (globalBgRegex.test(css)) {
    offenders.push('Found global `html`/`body` background declaration with white color.');
}

if (offenders.length > 0) {
    console.error('\nPre-commit check failed: global white background detected.');
    offenders.forEach((o) => console.error(' - ' + o));
    console.error('\nRemove or scope global backgrounds (use body.admin-login-page or component-level backgrounds), then commit.');
    process.exit(2);
}

console.log('Pre-commit checks passed.');
process.exit(0);
