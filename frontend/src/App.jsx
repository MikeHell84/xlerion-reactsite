
import React from 'react'
import '../src/styles/xlerion.scss'
import Navbar from './components/Navbar'
import Footer from './components/Footer'
import ContactForm from './components/ContactForm'

export default function App() {
  return (
    <div className="min-h-screen flex flex-col bg-light">
      <Navbar />
      <main className="flex-1 container-fluid px-3 px-md-4 py-3 py-md-5">
        <div className="row justify-content-center">
          <div className="col-12 col-md-10 col-lg-8">
            <h1 className="fw-bold mb-3 text-2xl md:text-3xl lg:text-4xl" style={{ fontFamily: 'var(--xlerion-font-sans)' }}>Xlerion React (placeholder)</h1>
            <p className="mb-4 text-muted">Construye y copia a <code>/public/build</code> con <code>npm run build</code>.</p>
            <section className="xlerion-card-main bg-white rounded-3 shadow-sm p-3 p-md-4 mb-4">
              <h2 className="h4 mb-3">Contacto (demo)</h2>
              <ContactForm />
            </section>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  )
}
