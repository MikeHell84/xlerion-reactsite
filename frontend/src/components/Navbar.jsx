import React from 'react'
import styles from './Navbar.module.scss'

export default function Navbar(){
  return (
    <nav className={styles.navbarRoot} aria-label="Main navigation">
      <a className={styles.brand} href="/">Xlerion</a>
      <div style={{marginLeft:'auto'}}>
        <a href="/inicio.php" className="nav-link">Inicio</a>
        <a href="/proyectos.php" className="nav-link" style={{marginLeft:12}}>Proyectos</a>
        <a href="/contacto.php" className="nav-link" style={{marginLeft:12}}>Contacto</a>
      </div>
    </nav>
  )
}
