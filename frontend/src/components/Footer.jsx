import React from 'react'
import styles from './Footer.module.scss'

export default function Footer(){
  return (
    <footer className={styles.footer}>
      <div>© {new Date().getFullYear()} Xlerion — Todos los derechos reservados.</div>
    </footer>
  )
}
