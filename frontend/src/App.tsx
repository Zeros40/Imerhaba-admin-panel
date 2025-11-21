import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import Landing from './pages/Landing'
import Scan from './pages/Scan'
import Review from './pages/Review'
import Details from './pages/Details'
import Outputs from './pages/Outputs'
import Results from './pages/Results'
import Projects from './pages/Projects'
import Admin from './pages/Admin'
import Navigation from './components/Navigation'

function App() {
  return (
    <Router>
      <div className="min-h-screen bg-gray-50">
        <Navigation />
        <Routes>
          <Route path="/" element={<Landing />} />
          <Route path="/scan" element={<Scan />} />
          <Route path="/projects/:projectId/review" element={<Review />} />
          <Route path="/projects/:projectId/details" element={<Details />} />
          <Route path="/projects/:projectId/outputs" element={<Outputs />} />
          <Route path="/projects/:projectId/results" element={<Results />} />
          <Route path="/projects" element={<Projects />} />
          <Route path="/admin" element={<Admin />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </div>
    </Router>
  )
}

export default App
