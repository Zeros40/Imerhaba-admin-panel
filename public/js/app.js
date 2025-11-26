// AI Agent Platform Frontend App
class AIAgentApp {
    constructor() {
        this.baseURL = window.location.origin;
        this.apiURL = `${this.baseURL}/api/v1`;
        this.token = localStorage.getItem('token');
        this.user = JSON.parse(localStorage.getItem('user') || 'null');
        this.currentPage = 'login';
        this.init();
    }

    init() {
        this.setupAxios();
        if (this.token && this.user) {
            this.showDashboard();
        } else {
            this.showAuthPage();
        }
    }

    setupAxios() {
        axios.defaults.baseURL = this.apiURL;
        if (this.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        }
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response?.status === 401) {
                    this.logout();
                }
                return Promise.reject(error);
            }
        );
    }

    showAuthPage() {
        const root = document.getElementById('root');
        root.innerHTML = `
            <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card" style="width: 100%; max-width: 400px;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <h2 style="color: #667eea; margin-bottom: 0.5rem;">AI Agent Platform</h2>
                        <p style="color: #666;">Build apps from prompts with AI</p>
                    </div>
                    <div id="auth-tabs" style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <button class="btn btn-primary" style="flex: 1;" onclick="app.showLoginForm()">Login</button>
                        <button class="btn btn-secondary" style="flex: 1;" onclick="app.showRegisterForm()">Register</button>
                    </div>
                    <div id="auth-form"></div>
                </div>
            </div>
        `;
        this.showLoginForm();
    }

    showLoginForm() {
        const form = document.getElementById('auth-form');
        form.innerHTML = `
            <form onsubmit="app.handleLogin(event)">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        `;
    }

    showRegisterForm() {
        const form = document.getElementById('auth-form');
        form.innerHTML = `
            <form onsubmit="app.handleRegister(event)">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" required>
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" id="first_name">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" id="last_name">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
        `;
    }

    async handleLogin(e) {
        e.preventDefault();
        try {
            const response = await axios.post('/auth/login', {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            });

            if (response.data.success) {
                this.token = response.data.data.token;
                this.user = response.data.data.user;
                localStorage.setItem('token', this.token);
                localStorage.setItem('user', JSON.stringify(this.user));
                this.setupAxios();
                this.showDashboard();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Login failed');
        }
    }

    async handleRegister(e) {
        e.preventDefault();
        try {
            const response = await axios.post('/auth/register', {
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
            });

            if (response.data.success) {
                alert('Registration successful! Please log in.');
                this.showLoginForm();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Registration failed');
        }
    }

    showDashboard() {
        const root = document.getElementById('root');
        root.innerHTML = `
            <div class="main-layout">
                <div class="navbar">
                    <div class="navbar-content">
                        <a href="#" class="navbar-brand" onclick="app.showPage('dashboard'); return false;">
                            🚀 AI Agent Platform
                        </a>
                        <ul class="navbar-menu">
                            <li><a href="#" onclick="app.showPage('dashboard'); return false;">Dashboard</a></li>
                            <li><a href="#" onclick="app.showPage('generate'); return false;">Generate</a></li>
                            <li><a href="#" onclick="app.showPage('projects'); return false;">Projects</a></li>
                            <li><a href="#" onclick="app.showPage('profile'); return false;">Profile</a></li>
                            <li><a href="#" onclick="app.logout(); return false;">Logout</a></li>
                        </ul>
                    </div>
                </div>
                <div class="main-content">
                    <div class="content" id="page-content"></div>
                </div>
            </div>
        `;
        this.showPage('dashboard');
    }

    showPage(page) {
        this.currentPage = page;
        const content = document.getElementById('page-content');

        switch (page) {
            case 'dashboard':
                this.showDashboardPage();
                break;
            case 'generate':
                this.showGeneratePage();
                break;
            case 'projects':
                this.showProjectsPage();
                break;
            case 'profile':
                this.showProfilePage();
                break;
        }
    }

    async showDashboardPage() {
        const content = document.getElementById('page-content');
        content.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

        try {
            const [statsRes, projectsRes] = await Promise.all([
                axios.get('/user/stats'),
                axios.get('/user/usage'),
            ]);

            const stats = statsRes.data.data;
            const usage = projectsRes.data.data;

            content.innerHTML = `
                <div class="container">
                    <h1 style="margin-bottom: 2rem;">Welcome, ${this.user.first_name || this.user.username}!</h1>

                    <div class="grid grid-3" style="margin-bottom: 2rem;">
                        <div class="card">
                            <h4 style="margin-bottom: 0.5rem;">${stats.total_projects}</h4>
                            <p>Total Projects</p>
                        </div>
                        <div class="card">
                            <h4 style="margin-bottom: 0.5rem;">${stats.total_generations}</h4>
                            <p>Generations</p>
                        </div>
                        <div class="card">
                            <h4 style="margin-bottom: 0.5rem;">${stats.total_tokens_used.toLocaleString()}</h4>
                            <p>Tokens Used</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Quick Stats</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Favorite Model:</strong> ${stats.favorite_model || 'N/A'}</p>
                            <p><strong>Current Month Requests:</strong> ${usage.total_requests || 0}</p>
                            <p><strong>Current Month Cost:</strong> $${(usage.cost || 0).toFixed(2)}</p>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button class="btn btn-primary btn-large" onclick="app.showPage('generate')">
                            Start Generating 🚀
                        </button>
                    </div>
                </div>
            `;
        } catch (error) {
            content.innerHTML = '<div class="alert alert-error">Failed to load dashboard</div>';
        }
    }

    showGeneratePage() {
        const content = document.getElementById('page-content');
        content.innerHTML = `
            <div class="container">
                <h1 style="margin-bottom: 2rem;">Generate App from Prompt</h1>

                <div class="grid grid-2" style="gap: 2rem;">
                    <div class="card">
                        <div class="card-header">
                            <h3>Create New Project</h3>
                        </div>
                        <form id="generation-form" onsubmit="app.handleGeneration(event)">
                            <div class="form-group">
                                <label>Project Name</label>
                                <input type="text" id="project_name" required placeholder="My Awesome App">
                            </div>

                            <div class="form-group">
                                <label>AI Model</label>
                                <select id="ai_model" required>
                                    <option value="gpt-4">GPT-4 (OpenAI)</option>
                                    <option value="gpt-4-turbo">GPT-4 Turbo (OpenAI)</option>
                                    <option value="claude-3-5-sonnet-20241022">Claude 3.5 Sonnet (Anthropic)</option>
                                    <option value="o1">OpenAI o1</option>
                                    <option value="o1-mini">OpenAI o1-mini</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>App Type</label>
                                <select id="app_type">
                                    <option value="web">Web App</option>
                                    <option value="react">React App</option>
                                    <option value="vue">Vue App</option>
                                    <option value="api">REST API</option>
                                    <option value="cli">CLI App</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Describe your app</label>
                                <textarea id="prompt" required placeholder="Describe what your app should do..."></textarea>
                                <div class="form-help">Be detailed and specific for better results</div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" id="generate-btn">
                                Generate Code ✨
                            </button>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Generated Code</h3>
                        </div>
                        <div id="code-output" style="background: #1f2937; color: #d1d5db; padding: 1rem; border-radius: 0.5rem; font-family: 'Courier New', monospace; max-height: 500px; overflow-y: auto;">
                            <p style="color: #6b7280;">Generated code will appear here...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async handleGeneration(e) {
        e.preventDefault();
        const btn = document.getElementById('generate-btn');
        const output = document.getElementById('code-output');

        btn.disabled = true;
        btn.textContent = 'Generating...';
        output.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

        try {
            const response = await axios.post('/generation/generate', {
                project_name: document.getElementById('project_name').value,
                prompt: document.getElementById('prompt').value,
                ai_model: document.getElementById('ai_model').value,
                app_type: document.getElementById('app_type').value,
            });

            if (response.data.success) {
                const code = response.data.data.code;
                output.innerHTML = `<pre style="white-space: pre-wrap; word-wrap: break-word;">${this.escapeHtml(code)}</pre>`;
                alert('App generated successfully! Check your projects.');
                document.getElementById('generation-form').reset();
            }
        } catch (error) {
            output.innerHTML = `<div class="alert alert-error">${error.response?.data?.message || 'Generation failed'}</div>`;
        } finally {
            btn.disabled = false;
            btn.textContent = 'Generate Code ✨';
        }
    }

    async showProjectsPage() {
        const content = document.getElementById('page-content');
        content.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

        try {
            const response = await axios.get('/projects');
            const projects = response.data.data.projects;

            let html = `
                <div class="container">
                    <h1 style="margin-bottom: 2rem;">Your Projects</h1>
            `;

            if (projects.length === 0) {
                html += '<div class="alert alert-info">No projects yet. <a href="#" onclick="app.showPage(\'generate\'); return false;">Create one!</a></div>';
            } else {
                html += '<div class="grid grid-2">';
                projects.forEach(project => {
                    html += `
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h4>${project.title}</h4>
                                    <span class="badge badge-${project.status === 'completed' ? 'success' : 'warning'}">${project.status}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>${project.description || 'No description'}</p>
                                <p><strong>Model:</strong> ${project.ai_model}</p>
                                <p><strong>Created:</strong> ${new Date(project.created_at).toLocaleDateString()}</p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary btn-small" onclick="app.viewProject(${project.id})">View</button>
                                <button class="btn btn-danger btn-small" onclick="app.deleteProject(${project.id})">Delete</button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            html += '</div>';
            content.innerHTML = html;
        } catch (error) {
            content.innerHTML = '<div class="alert alert-error">Failed to load projects</div>';
        }
    }

    async showProfilePage() {
        const content = document.getElementById('page-content');
        content.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

        try {
            const response = await axios.get('/user/profile');
            const profile = response.data.data;

            content.innerHTML = `
                <div class="container">
                    <h1 style="margin-bottom: 2rem;">Profile</h1>

                    <div class="grid grid-2" style="gap: 2rem;">
                        <div class="card">
                            <div class="card-header">
                                <h3>User Information</h3>
                            </div>
                            <form onsubmit="app.updateProfile(event)">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" value="${profile.username}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" value="${profile.email}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" id="first_name" value="${profile.first_name || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" id="last_name" value="${profile.last_name || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Bio</label>
                                    <textarea id="bio">${profile.bio || ''}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                            </form>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3>Account</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Plan:</strong> ${profile.plan || 'Free'}</p>
                                <p><strong>Member Since:</strong> ${new Date(profile.created_at).toLocaleDateString()}</p>
                                <p><strong>Total Requests:</strong> ${profile.stats.total_requests}</p>
                                <p><strong>Total Cost:</strong> $${(profile.stats.total_cost || 0).toFixed(2)}</p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-danger" onclick="app.changePassword()">Change Password</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            content.innerHTML = '<div class="alert alert-error">Failed to load profile</div>';
        }
    }

    async updateProfile(e) {
        e.preventDefault();
        try {
            await axios.put('/user/profile', {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                bio: document.getElementById('bio').value,
            });
            alert('Profile updated successfully!');
            this.showProfilePage();
        } catch (error) {
            alert(error.response?.data?.message || 'Update failed');
        }
    }

    async viewProject(projectId) {
        try {
            const response = await axios.get(`/projects/${projectId}`);
            const project = response.data.data;

            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal">
                    <div class="modal-header">
                        <h3>${project.title}</h3>
                        <button type="button" class="btn-close" onclick="this.closest('.modal-overlay').remove()" style="border: none; background: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Status:</strong> <span class="badge badge-${project.status === 'completed' ? 'success' : 'warning'}">${project.status}</span></p>
                        <p><strong>Model:</strong> ${project.ai_model}</p>
                        <p><strong>Description:</strong> ${project.description || 'N/A'}</p>
                        <p><strong>Created:</strong> ${new Date(project.created_at).toLocaleDateString()}</p>
                        <p><strong>Generated Apps:</strong> ${project.generated_apps.length}</p>

                        ${project.generated_code ? `
                            <hr>
                            <h4>Generated Code</h4>
                            <pre style="background: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; max-height: 300px;">${this.escapeHtml(project.generated_code)}</pre>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" onclick="this.closest('.modal-overlay').remove()">Close</button>
                        <button class="btn btn-primary" onclick="app.downloadProject(${projectId}); this.closest('.modal-overlay').remove();">Download</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } catch (error) {
            alert('Failed to load project');
        }
    }

    async deleteProject(projectId) {
        if (confirm('Are you sure you want to delete this project?')) {
            try {
                await axios.delete(`/projects/${projectId}`);
                alert('Project deleted successfully!');
                this.showProjectsPage();
            } catch (error) {
                alert(error.response?.data?.message || 'Delete failed');
            }
        }
    }

    async downloadProject(projectId) {
        try {
            const response = await axios.get(`/projects/${projectId}/export`);
            const code = response.data.data.code;
            const blob = new Blob([code], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `project-${projectId}.code`;
            a.click();
        } catch (error) {
            alert('Download failed');
        }
    }

    changePassword() {
        const newPassword = prompt('Enter new password:');
        if (newPassword && newPassword.length >= 8) {
            const oldPassword = prompt('Enter current password:');
            if (oldPassword) {
                axios.post('/auth/change-password', {
                    old_password: oldPassword,
                    new_password: newPassword,
                }).then(() => {
                    alert('Password changed successfully!');
                }).catch(error => {
                    alert(error.response?.data?.message || 'Password change failed');
                });
            }
        }
    }

    async logout() {
        try {
            await axios.post('/auth/logout');
        } catch (error) {
            // Continue logout even if request fails
        }
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.token = null;
        this.user = null;
        this.showAuthPage();
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}

// Initialize app when DOM is ready
let app;
document.addEventListener('DOMContentLoaded', () => {
    app = new AIAgentApp();
});
