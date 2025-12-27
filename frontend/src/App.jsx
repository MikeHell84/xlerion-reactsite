import React from 'react'
import '../src/styles/xlerion.scss'
import Navbar from './components/Navbar'
import Footer from './components/Footer'
import ContactForm from './components/ContactForm'

export default function App(){
  return (
    <div>
      <Navbar />
      <main style={{padding:20}}>
        <h1>Xlerion React (placeholder)</h1>
        <p>Construye y copia a <code>/public/build</code> con <code>npm run build</code>.</p>
        <section style={{maxWidth:800}}>
          <h2>Contacto (demo)</h2>
          <ContactForm />
        </section>
      </main>
      <Footer />
    </div>
  )
}
