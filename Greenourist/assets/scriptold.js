

  // Fetch and inject the auth-buttons.html file
  fetch('auth-buttons.html')
    .then(response => response.text())
    .then(html => {
      document.getElementById('auth-buttons-placeholder').innerHTML = html;
    })
    .catch(err => {
      console.error('Failed to load auth buttons:', err);
    });
   
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all elements with fade-in-up class
        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        // Header background change on scroll
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(45, 80, 22, 0.95)';
            } else {
                header.style.background = 'linear-gradient(135deg, #2d5016, #4a7c25)';
            }
        });

        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
       
    const activitySchema = new mongoose.Schema({
  title: String,
  description: String,
  difficulty: {
    type: String,
    enum: ['easy', 'medium', 'hard'],
    required: true
  },
  date: {
    type: Date,
    required: true
  },
  isStarred: {
    type: Boolean,
    default: false
  },
  // other fields...
});

// When rendering activities, include these attributes
activitySchema.methods.toJSON = function() {
  const activity = this.toObject();
  activity.filterAttributes = {
    difficulty: this.difficulty,
    date: this.date.toISOString(),
    isStarred: this.isStarred
  };
  return activity;
};




function renderActivity(activity) {
  const activityElement = document.createElement('div');
  activityElement.className = 'activity-item';
  
  // Add filter attributes
  activityElement.dataset.difficulty = activity.difficulty;
  activityElement.dataset.date = activity.date;
  if (activity.isStarred) {
    activityElement.classList.add('starred');
  }
  
  // Add content
  activityElement.innerHTML = `
    <h3>${activity.title}</h3>
    <p>${activity.description}</p>
    <span class="difficulty-tag ${activity.difficulty}">
      ${activity.difficulty.charAt(0).toUpperCase() + activity.difficulty.slice(1)}
    </span>
    <button class="star-button" data-activity-id="${activity.id}">
      ${activity.isStarred ? '★' : '☆'}
    </button>
  `;
  
  return activityElement;
}




document.getElementById('activity-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = {
    title: this.title.value,
    description: this.description.value,
    difficulty: this.difficulty.value,
    date: this.date.value,
    isStarred: this.isStarred.checked
  };
  
  try {
    const response = await fetch('/api/activities', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData)
    });
    
    const newActivity = await response.json();
    
    // Add to DOM with proper filter attributes
    const activitiesList = document.querySelector('.activities-list');
    activitiesList.appendChild(renderActivity(newActivity));
    
    // Reset form
    this.reset();
  } catch (error) {
    console.error('Error adding activity:', error);
  }
});


function ensureFilterAttributes(activityElement) {
  if (!activityElement.dataset.difficulty) {
    const difficultyTag = activityElement.querySelector('.difficulty-tag');
    if (difficultyTag) {
      activityElement.dataset.difficulty = difficultyTag.textContent.trim().toLowerCase();
    } else {
      activityElement.dataset.difficulty = 'medium'; // default
    }
  }
  
  if (!activityElement.dataset.date) {
    const dateElement = activityElement.querySelector('.activity-date');
    if (dateElement) {
      activityElement.dataset.date = new Date(dateElement.textContent).toISOString();
    } else {
      activityElement.dataset.date = new Date().toISOString(); // default to now
    }
  }
  
  if (activityElement.querySelector('.star-button').textContent.includes('★')) {
    activityElement.classList.add('starred');
  }
}

// Run this on all activities when page loads
document.querySelectorAll('.activity-item').forEach(ensureFilterAttributes);
