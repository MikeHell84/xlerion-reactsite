import React from 'react';

const About = () => {
  return (
    <section id="about" className="py-5 bg-light">
      <div className="container">
        <div className="row">
          <div className="col-lg-8 mx-auto text-center">
            <h2 className="display-4 fw-bold mb-4">About Xlerion</h2>
            <p className="lead text-muted mb-4">
              We are a forward-thinking company dedicated to delivering 
              innovative solutions that drive success.
            </p>
          </div>
        </div>
        <div className="row mt-5">
          <div className="col-md-4 mb-4">
            <div className="card h-100 border-0 shadow-sm">
              <div className="card-body text-center p-4">
                <div className="mb-3">
                  <i className="bi bi-rocket-takeoff text-primary" style={{ fontSize: '3rem' }}></i>
                </div>
                <h5 className="card-title fw-bold">Innovation</h5>
                <p className="card-text text-muted">
                  We stay ahead of the curve with cutting-edge technology 
                  and creative solutions.
                </p>
              </div>
            </div>
          </div>
          <div className="col-md-4 mb-4">
            <div className="card h-100 border-0 shadow-sm">
              <div className="card-body text-center p-4">
                <div className="mb-3">
                  <i className="bi bi-award text-primary" style={{ fontSize: '3rem' }}></i>
                </div>
                <h5 className="card-title fw-bold">Excellence</h5>
                <p className="card-text text-muted">
                  Quality is at the heart of everything we do, 
                  ensuring superior results.
                </p>
              </div>
            </div>
          </div>
          <div className="col-md-4 mb-4">
            <div className="card h-100 border-0 shadow-sm">
              <div className="card-body text-center p-4">
                <div className="mb-3">
                  <i className="bi bi-people text-primary" style={{ fontSize: '3rem' }}></i>
                </div>
                <h5 className="card-title fw-bold">Collaboration</h5>
                <p className="card-text text-muted">
                  We work closely with our clients to understand and 
                  exceed their expectations.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default About;
