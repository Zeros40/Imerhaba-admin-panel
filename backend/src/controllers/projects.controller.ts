import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';
import { v4 as uuidv4 } from 'uuid';
import { scrapeWebsite } from '../services/scraper.service';
import { extractFromWebsite, getExtractedProfile } from '../services/extraction.service';
import { generateContent, getGeneratedOutputs } from '../services/generation.service';
import { exportOutputs, ExportFormat } from '../services/export.service';

const prisma = new PrismaClient();

// Create new project
export async function createProject(req: Request, res: Response) {
  try {
    const { websiteUrl, name } = req.body;

    if (!websiteUrl) {
      return res.status(400).json({ error: 'Website URL is required' });
    }

    // For now, use a default user ID (in production, extract from auth token)
    const userId = req.body.userId || 'demo-user-' + uuidv4();

    // Create project
    const project = await prisma.project.create({
      data: {
        name: name || new URL(websiteUrl).hostname,
        websiteUrl,
        userId,
      },
    });

    // Create empty business profile
    await prisma.businessProfile.create({
      data: {
        projectId: project.id,
      },
    });

    res.json({ success: true, projectId: project.id });
  } catch (error) {
    console.error('Create project error:', error);
    res.status(500).json({ error: 'Failed to create project' });
  }
}

// Get project details
export async function getProject(req: Request, res: Response) {
  try {
    const { projectId } = req.params;

    const project = await prisma.project.findUnique({
      where: { id: projectId },
      include: { businessProfile: true },
    });

    if (!project) {
      return res.status(404).json({ error: 'Project not found' });
    }

    res.json(project);
  } catch (error) {
    console.error('Get project error:', error);
    res.status(500).json({ error: 'Failed to fetch project' });
  }
}

// Scan and extract website
export async function scanWebsite(req: Request, res: Response) {
  try {
    const { projectId } = req.params;

    const project = await prisma.project.findUnique({
      where: { id: projectId },
    });

    if (!project) {
      return res.status(404).json({ error: 'Project not found' });
    }

    // Scrape website
    res.json({ status: 'scanning', message: 'Scraping website content...' });

    const websiteContent = await scrapeWebsite(project.websiteUrl);

    // Extract business profile
    const profile = await extractFromWebsite(project.websiteUrl, websiteContent, projectId);

    // Notify client (in real app, use WebSockets or polling)
    res.json({
      status: 'extracted',
      message: 'Website analysis complete',
      profile,
    });
  } catch (error) {
    console.error('Scan error:', error);
    res.status(500).json({
      error: 'Failed to scan website',
      message: error instanceof Error ? error.message : 'Unknown error',
    });
  }
}

// Get extracted profile
export async function getProfile(req: Request, res: Response) {
  try {
    const { projectId } = req.params;

    const profile = await getExtractedProfile(projectId);

    if (!profile) {
      return res.status(404).json({ error: 'Profile not found' });
    }

    res.json(profile);
  } catch (error) {
    console.error('Get profile error:', error);
    res.status(500).json({ error: 'Failed to fetch profile' });
  }
}

// Generate outputs
export async function generateOutputs(req: Request, res: Response) {
  try {
    const { projectId } = req.params;
    const { outputTypes, optionalDetails, language } = req.body;

    const profile = await getExtractedProfile(projectId);

    if (!profile) {
      return res.status(400).json({ error: 'No business profile found. Scan website first.' });
    }

    const results: Record<string, string> = {};

    for (const outputType of outputTypes) {
      try {
        const content = await generateContent(
          outputType,
          profile,
          projectId,
          optionalDetails,
          language || 'en'
        );
        results[outputType] = content;
      } catch (error) {
        console.error(`Failed to generate ${outputType}:`, error);
        results[outputType] = '';
      }
    }

    res.json({ status: 'complete', results });
  } catch (error) {
    console.error('Generate outputs error:', error);
    res.status(500).json({ error: 'Failed to generate outputs' });
  }
}

// Get all outputs
export async function getOutputs(req: Request, res: Response) {
  try {
    const { projectId } = req.params;

    const outputs = await getGeneratedOutputs(projectId);

    res.json(outputs);
  } catch (error) {
    console.error('Get outputs error:', error);
    res.status(500).json({ error: 'Failed to fetch outputs' });
  }
}

// Export outputs
export async function exportProject(req: Request, res: Response) {
  try {
    const { projectId } = req.params;
    const { format = 'pdf' } = req.query;

    const project = await prisma.project.findUnique({
      where: { id: projectId },
      include: { businessProfile: true },
    });

    if (!project) {
      return res.status(404).json({ error: 'Project not found' });
    }

    const outputs = await getGeneratedOutputs(projectId);

    const exportData = {
      projectName: project.name,
      businessName: project.businessProfile?.businessName,
      outputs: outputs.map((o) => ({
        title: o.title,
        content: o.content,
        type: o.type,
      })),
      generatedAt: new Date(),
    };

    const buffer = await exportOutputs(exportData, format as ExportFormat);

    const mimeTypes = {
      pdf: 'application/pdf',
      html: 'text/html',
      docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    };

    res.setHeader('Content-Type', mimeTypes[format as keyof typeof mimeTypes] || 'application/octet-stream');
    res.setHeader('Content-Disposition', `attachment; filename="${project.name}-zodiac13.${format}"`);
    res.send(buffer);
  } catch (error) {
    console.error('Export error:', error);
    res.status(500).json({ error: 'Failed to export project' });
  }
}

// List user projects
export async function listProjects(req: Request, res: Response) {
  try {
    const userId = req.query.userId as string || 'demo-user-default';

    const projects = await prisma.project.findMany({
      where: { userId },
      include: { businessProfile: true },
      orderBy: { createdAt: 'desc' },
    });

    res.json(projects);
  } catch (error) {
    console.error('List projects error:', error);
    res.status(500).json({ error: 'Failed to list projects' });
  }
}

// Delete project
export async function deleteProject(req: Request, res: Response) {
  try {
    const { projectId } = req.params;

    await prisma.project.delete({
      where: { id: projectId },
    });

    res.json({ success: true });
  } catch (error) {
    console.error('Delete project error:', error);
    res.status(500).json({ error: 'Failed to delete project' });
  }
}
