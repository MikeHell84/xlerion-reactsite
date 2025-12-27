import React from 'react';

const Services = () => {
  const services = [
    {
      title: 'Web Development',
      description: 'Custom web applications built with modern technologies',
      icon: 'bi-code-slash'
    },
    {
      title: 'Mobile Solutions',
      description: 'Native and cross-platform mobile applications',
      icon: 'bi-phone'
    },
    {
      title: 'Cloud Services',
      description: 'Scalable cloud infrastructure and deployment',
      icon: 'bi-cloud'
    },
    {
      title: 'Database Design',
      description: 'Optimized database architecture and management',
      icon: 'bi-database'
    },
    {
      title: 'API Development',
      description: 'RESTful and GraphQL API solutions',
      icon: 'bi-gear'
    },
    {
      title: 'Consulting',
      description: 'Expert technical guidance and strategy',
      icon: 'bi-lightbulb'
    }
  ];

  return (
    <section id="services" className="py-5">
      <div className="container">
        <div className="row">
          <div className="col-lg-8 mx-auto text-center mb-5">
            <h2 className="display-4 fw-bold mb-4">Our Services</h2>
            <p className="lead text-muted">
              Comprehensive solutions tailored to your business needs
            </p>
          </div>
        </div>
        <div className="row">
          {services.map((service, index) => (
            <div key={index} className="col-md-6 col-lg-4 mb-4">
              <div className="card h-100 border-0 shadow-sm hover:shadow-lg transition">
                <div className="card-body p-4">
                  <div className="d-flex align-items-center mb-3">
                    <div className="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                      <i className={`${service.icon} text-primary`} style={{ fontSize: '1.5rem' }}></i>
                    </div>
                    <h5 className="card-title mb-0 fw-bold">{service.title}</h5>
                  </div>
                  <p className="card-text text-muted">{service.description}</p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Services;
