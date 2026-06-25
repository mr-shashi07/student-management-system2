// Bodhivaas UI helpers
document.addEventListener('DOMContentLoaded', function(){
  // Theme toggle
  const themeToggle = document.querySelector('[data-toggle-theme]');
  if(themeToggle){
    themeToggle.addEventListener('click', (e)=>{
      const html = document.documentElement;
      const current = html.getAttribute('data-theme');
      html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
      localStorage.setItem('bodhivaas-theme', html.getAttribute('data-theme'));
      themeToggle.classList.toggle('active');
    });
    const saved = localStorage.getItem('bodhivaas-theme');
    if(saved) document.documentElement.setAttribute('data-theme', saved);
  }

  // Sidebar collapse on mobile
  const sbToggle = document.querySelector('[data-toggle-sidebar]');
  if(sbToggle){
    sbToggle.addEventListener('click', ()=>{
      document.body.classList.toggle('sidebar-collapsed');
    });
  }

});

// Chart helper
function createLineChart(ctx, labels, data, options={}){
  return new Chart(ctx, {
    type: 'line',
    data: {labels: labels, datasets: [{label: options.label||'', data: data, borderWidth:2, tension:0.35, borderColor: getComputedStyle(document.documentElement).getPropertyValue('--brand') || '#6c5ce7', fill: true, backgroundColor: 'rgba(108,92,231,0.08)'}]},
    options: Object.assign({plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}, options)
  });
}
