import React, {useState} from 'react'

export default function ContactForm(){
  const [status, setStatus] = useState(null)
  const handleSubmit = async (e) => {
    e.preventDefault()
    const fd = new FormData(e.target)
    setStatus('sending')
    try{
      const res = await fetch('/api/contact.php', {method:'POST', body: fd, headers:{'Accept':'application/json'}})
      const data = await res.json()
      if(res.ok) setStatus('ok')
      else setStatus('error')
    }catch(err){
      setStatus('error')
    }
  }
  return (
    <form onSubmit={handleSubmit}>
      <div className="mb-3"><input name="name" className="form-control" placeholder="Nombre" required/></div>
      <div className="mb-3"><input name="email" type="email" className="form-control" placeholder="Correo" required/></div>
      <div className="mb-3"><textarea name="message" className="form-control" rows="4" placeholder="Mensaje" required></textarea></div>
      <button className="btn btn-primary" type="submit">Enviar</button>
      {status === 'sending' && <div className="muted">Enviando…</div>}
      {status === 'ok' && <div className="text-success">Mensaje enviado</div>}
      {status === 'error' && <div className="text-danger">Error enviando</div>}
    </form>
  )
}
