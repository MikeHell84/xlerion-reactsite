import React from 'react';

const Hero = () => {
  return (
    <section id="home" className="text-white py-5" style={{ background: 'linear-gradient(to right, #0d6efd, #6610f2)' }}>
      <div className="container">
        <div className="row align-items-center min-vh-75">
          <div className="col-lg-6">
            <h1 className="display-3 fw-bold mb-4">
              Welcome to Xlerion
            </h1>
            <p className="lead mb-4">
              Your gateway to innovative solutions and cutting-edge technology. 
              We deliver excellence in every project.
            </p>
            <div className="d-flex gap-3">
              <a href="#services" className="btn btn-light btn-lg">
                Our Services
              </a>
              <a href="#contact" className="btn btn-outline-light btn-lg">
                Get in Touch
              </a>
            </div>
          </div>
          <div className="col-lg-6 d-none d-lg-block">
            <div className="text-center">
              <div className="bg-white bg-opacity-10 rounded-3 p-5">
                <i className="bi bi-lightning-charge-fill display-1"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
