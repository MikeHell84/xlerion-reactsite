import React from 'react';

const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-dark text-white mt-5">
      <div className="container py-4">
        <div className="row">
          <div className="col-md-4 mb-3 mb-md-0">
            <h5 className="fw-bold">Xlerion</h5>
            <p className="text-muted">
              Innovation meets excellence
            </p>
          </div>
          <div className="col-md-4 mb-3 mb-md-0">
            <h6 className="fw-bold">Quick Links</h6>
            <ul className="list-unstyled">
              <li>
                <a href="#home" className="text-muted text-decoration-none hover:text-white">
                  Home
                </a>
              </li>
              <li>
                <a href="#about" className="text-muted text-decoration-none hover:text-white">
                  About
                </a>
              </li>
              <li>
                <a href="#services" className="text-muted text-decoration-none hover:text-white">
                  Services
                </a>
              </li>
              <li>
                <a href="#contact" className="text-muted text-decoration-none hover:text-white">
                  Contact
                </a>
              </li>
            </ul>
          </div>
          <div className="col-md-4">
            <h6 className="fw-bold">Connect</h6>
            <p className="text-muted">
              Follow us on social media
            </p>
            <div className="d-flex gap-3">
              <a href="#" className="text-muted hover:text-white">
                <i className="bi bi-facebook"></i>
              </a>
              <a href="#" className="text-muted hover:text-white">
                <i className="bi bi-twitter"></i>
              </a>
              <a href="#" className="text-muted hover:text-white">
                <i className="bi bi-linkedin"></i>
              </a>
            </div>
          </div>
        </div>
        <hr className="my-4 border-secondary" />
        <div className="text-center text-muted">
          <p className="mb-0">
            &copy; {currentYear} Xlerion. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
