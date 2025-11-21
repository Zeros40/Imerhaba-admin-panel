import { Router } from 'express';
import {
  createProject,
  getProject,
  scanWebsite,
  getProfile,
  generateOutputs,
  getOutputs,
  exportProject,
  listProjects,
  deleteProject,
} from '../controllers/projects.controller';

const router = Router();

// Project management
router.post('/', createProject);
router.get('/', listProjects);
router.get('/:projectId', getProject);
router.delete('/:projectId', deleteProject);

// Extraction
router.post('/:projectId/scan', scanWebsite);
router.get('/:projectId/profile', getProfile);

// Generation
router.post('/:projectId/generate', generateOutputs);
router.get('/:projectId/outputs', getOutputs);

// Export
router.get('/:projectId/export', exportProject);

export default router;
