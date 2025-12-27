import React from 'react'
import '../src/styles/xlerion.scss'
import styles from './components/Footer.module.scss'

export default function App(){
  return (
    <div style={{padding:20}}>
      <h1>Xlerion React (placeholder)</h1>
      <p>Construye y copia a <code>/public/build</code> con <code>npm run build</code>.</p>
      <footer className={styles.footer}>
        <small>© 2025 Xlerion</small>
      </footer>
    </div>
  )
}
