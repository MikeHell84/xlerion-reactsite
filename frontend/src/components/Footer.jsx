import React, { useState } from 'react'
import styles from './Footer.module.scss'

// Simple SVG placeholders for social icons (kept minimal to avoid deps)
const Icon = ({ children, label }) => (
  <span aria-hidden="true" title={label} style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: 20, height: 20 }}>
    {children}
  </span>
)

export default function Footer() {
  const [email, setEmail] = useState('')
  const [msg, setMsg] = useState(null)

  const validateEmail = (v) => /\S+@\S+\.\S+/.test(v)

  const onSubscribe = (e) => {
    e.preventDefault()
    setMsg(null)
    if (!email.trim()) { setMsg({ type: 'error', text: 'El correo es obligatorio.' }); return }
    if (!validateEmail(email)) { setMsg({ type: 'error', text: 'Correo inválido.' }); return }

    // Simular suscripción (puedes reemplazar aquí por fetch a tu endpoint)
    setTimeout(() => {
      setMsg({ type: 'ok', text: 'Gracias por suscribirte.' })
      setEmail('')
    }, 600)
  }

  const smoothScroll = (e, href) => {
    if (href && href.startsWith('#')) {
      e.preventDefault()
      const el = document.querySelector(href)
      if (el) el.scrollIntoView({ behavior: 'smooth' })
    }
  }

  return (
    <footer className={`${styles['footer-container']} footer bg-dark`}>
      <div className="container">
        <div className="row">
          <div className="col-12 col-md-3 mb-4">
            <h5 className="text-white">Contacto</h5>
            <address className="mb-2">
              Calle Falsa 123<br />Madrid, España
            </address>
            <div className="mb-1">Tel: <a href="tel:+34123456789">+34 123 456 789</a></div>
            <div>Email: <a href="mailto:info@xlerion.com">info@xlerion.com</a></div>
            <div className="mt-2 small">Horario: Lun-Vie 09:00–18:00</div>
          </div>

          <div className="col-12 col-md-3 mb-4">
            <h5 className="text-white">Enlaces rápidos</h5>
            <ul className={`list-unstyled ${styles['footer-links']}`}>
              <li><a href="/inicio.php" onClick={(e) => smoothScroll(e, '#top')}>Inicio</a></li>
              <li><a href="/soluciones.php">Servicios</a></li>
              <li><a href="/proyectos.php">Proyectos</a></li>
              <li><a href="/blog.php">Blog</a></li>
              <li><a href="/contacto.php">Contacto</a></li>
              <li><a href="/legal.php">Políticas</a></li>
            </ul>
          </div>

          <div className="col-12 col-md-3 mb-4">
            <h5 className="text-white">Redes sociales</h5>
            <div className={styles['footer-social']}>
              <a className="footer-social-item" href="https://www.linkedin.com" target="_blank" rel="noreferrer" aria-label="LinkedIn">
                <Icon label="LinkedIn">in</Icon>
              </a>
              <a className="footer-social-item" href="https://www.instagram.com" target="_blank" rel="noreferrer" aria-label="Instagram">
                <Icon label="Instagram">ig</Icon>
              </a>
              <a className="footer-social-item" href="https://www.facebook.com" target="_blank" rel="noreferrer" aria-label="Facebook">
                <Icon label="Facebook">f</Icon>
              </a>
              <a className="footer-social-item" href="https://www.behance.net" target="_blank" rel="noreferrer" aria-label="Behance">
                <Icon label="Behance">B</Icon>
              </a>
              <a className="footer-social-item" href="https://www.kickstarter.com" target="_blank" rel="noreferrer" aria-label="Kickstarter">
                <Icon label="Kickstarter">K</Icon>
              </a>
              <a className="footer-social-item" href="https://www.patreon.com" target="_blank" rel="noreferrer" aria-label="Patreon">
                <Icon label="Patreon">P</Icon>
              </a>
            </div>
          </div>

          <div className="col-12 col-md-3 mb-4">
            <h5 className="text-white">Newsletter</h5>
            <div className={styles['footer-newsletter']}>
              <form onSubmit={onSubscribe} aria-label="Formulario suscripción newsletter">
                <input type="email" placeholder="tu@correo.com" value={email} onChange={e => setEmail(e.target.value)} aria-label="Correo" />
                <button type="submit">Suscribir</button>
              </form>
              {msg && <div className="msg" style={{ color: msg.type === 'error' ? '#ffb3b3' : '#ccead9' }}>{msg.text}</div>}
            </div>
          </div>
        </div>

        <div className={styles['footer-bottom'] + ' footer-bottom'}>
          Soluciones modulares que empoderan la cultura y la tecnología.
        </div>
      </div>
    </footer>
  )
}

