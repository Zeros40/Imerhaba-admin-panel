import { NextRequest, NextResponse } from 'next/server';

export async function POST(request: NextRequest) {
  try {
    const { tool, prompt } = await request.json();

    // In production, you would call OpenAI API here
    // For now, we'll use a mock response
    const openaiApiKey = process.env.OPENAI_API_KEY;

    if (!openaiApiKey) {
      // Mock response for demo purposes
      const mockResponses: Record<string, string> = {
        'youtube-kit': generateYouTubeMockResponse(prompt),
        'caption-generator': generateCaptionMockResponse(prompt),
        'cold-outreach': generateOutreachMockResponse(prompt),
        'post-scheduler': generateSchedulerMockResponse(prompt),
        'chatbot-script': generateChatbotMockResponse(prompt),
        'lead-magnet': generateLeadMagnetMockResponse(prompt),
        'proposal-generator': generateProposalMockResponse(prompt),
        'crm-followup': generateFollowUpMockResponse(prompt),
      };

      return NextResponse.json({
        content: mockResponses[tool] || 'Generated content will appear here.',
      });
    }

    // Real OpenAI API call
    const response = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${openaiApiKey}`,
      },
      body: JSON.stringify({
        model: 'gpt-4',
        messages: [
          {
            role: 'system',
            content: 'You are a professional content creator and marketing expert.',
          },
          {
            role: 'user',
            content: prompt,
          },
        ],
        temperature: 0.7,
        max_tokens: 2000,
      }),
    });

    const data = await response.json();
    const content = data.choices[0]?.message?.content || 'No content generated';

    return NextResponse.json({ content });
  } catch (error) {
    console.error('Error generating content:', error);
    return NextResponse.json(
      { error: 'Failed to generate content' },
      { status: 500 }
    );
  }
}

// Mock response generators for demo
function generateYouTubeMockResponse(prompt: string): string {
  return `🎬 YOUTUBE VIDEO SCRIPT

📌 HOOK (0:00-0:15)
"Wait... you can make money on YouTube without EVER showing your face? Let me show you exactly how."

📖 INTRODUCTION (0:15-0:45)
Welcome! In today's video, we're diving into the world of faceless YouTube channels. Whether you're camera-shy or just want to maintain privacy, this guide will show you 7 proven strategies that are working RIGHT NOW.

🎯 MAIN CONTENT

Section 1: Choose Your Niche (0:45-2:00)
- Focus on evergreen topics
- Research what's trending
- Find your unique angle
[B-ROLL: Screen recordings of YouTube analytics, trending topics]

Section 2: Content Creation Strategy (2:00-4:00)
- Use stock footage effectively
- Leverage AI voiceover tools
- Create engaging animations
[B-ROLL: Examples of successful faceless channels]

Section 3: Monetization Methods (4:00-6:00)
- Ad revenue breakdown
- Affiliate marketing opportunities
- Sponsored content strategies
[B-ROLL: Revenue screenshots, product links]

Section 4: Growth Hacks (6:00-8:00)
- SEO optimization tips
- Thumbnail psychology
- Consistency is key
[B-ROLL: Analytics growth charts]

Section 5: Tools & Resources (8:00-9:30)
- Video editing software
- Stock footage libraries
- AI voiceover platforms
[B-ROLL: Tool demonstrations]

📣 CALL-TO-ACTION (9:30-9:50)
"If you found this helpful, smash that like button and subscribe for more content creation tips. Drop a comment below telling me which strategy you'll try first!"

🎬 OUTRO (9:50-10:00)
"Thanks for watching! See you in the next video where we'll dive deep into AI voiceover tools."
[B-ROLL: Channel logo animation]`;
}

function generateCaptionMockResponse(prompt: string): string {
  return `✨ Your success story starts with a single step! 💫

We're thrilled to announce our newest collection designed specifically for ambitious entrepreneurs like YOU.

Each piece tells a story of dedication, growth, and transformation. 🚀

What makes this special?
✅ Handcrafted with passion
✅ Sustainable & ethical
✅ Limited edition release
✅ Ships worldwide

Tag someone who needs to see this! 👇

#Entrepreneur #Success #GrowthMindset #BusinessGoals #Motivation #Hustle #DreamBig #Achievement #SuccessStory #Inspiration #SmallBusiness #StartupLife`;
}

function generateOutreachMockResponse(prompt: string): string {
  return `Subject: Quick question about [Company Name]'s growth goals

Hi [Name],

I hope this email finds you well. I came across [Company Name] and was impressed by your recent [specific achievement or project].

I specialize in helping companies like yours [specific benefit/outcome], and I noticed an opportunity where we might be able to collaborate.

We recently helped [similar company] achieve [specific result], and I believe we could deliver similar value for [Company Name].

Would you be open to a brief 15-minute call next week to explore how we might work together?

Best regards,
[Your Name]

P.S. - No pressure at all. If the timing isn't right, I completely understand.`;
}

function generateSchedulerMockResponse(prompt: string): string {
  return `📅 CONTENT CALENDAR - 1 WEEK PLAN

MONDAY
Post: Educational Tip
Time: 9:00 AM
Format: Carousel post (5-7 slides)
Topic: "5 Common Mistakes in [Your Niche]"
Engagement: Ask followers to share their experience

TUESDAY
Post: Behind-the-Scenes
Time: 2:00 PM
Format: Story + Feed Post
Topic: Your daily routine/process
Engagement: Poll in stories

WEDNESDAY
Post: Industry News
Time: 10:00 AM
Format: Single image with caption
Topic: Latest trend commentary
Engagement: Ask for opinions

THURSDAY
Post: Testimonial/Case Study
Time: 1:00 PM
Format: Before/After graphics
Topic: Client success story
Engagement: Call-to-action to DM

FRIDAY
Post: Value Bomb
Time: 11:00 AM
Format: Video (60 sec)
Topic: Quick actionable tip
Engagement: Save & Share CTA

SATURDAY
Post: Community Engagement
Time: 3:00 PM
Format: Interactive post
Topic: Weekend motivation
Engagement: Comment with goals

SUNDAY
Post: Week Recap
Time: 5:00 PM
Format: Carousel
Topic: Top 3 lessons from the week
Engagement: Discussion starter

💡 BEST PRACTICES:
- Use relevant hashtags (20-30)
- Respond to comments within 1 hour
- Engage with your audience's content
- Track analytics weekly`;
}

function generateChatbotMockResponse(prompt: string): string {
  return `🤖 CHATBOT CONVERSATION SCRIPT

WELCOME MESSAGE:
"Hi there! 👋 Welcome to [Business Name]. I'm here to help you 24/7. How can I assist you today?"

Options:
1️⃣ Product Information
2️⃣ Pricing & Plans
3️⃣ Support
4️⃣ Speak to Human

---

PATH 1: PRODUCT INFORMATION
User: "Tell me about your products"
Bot: "Great question! We offer [brief description]. Which category interests you most?"
- [Category A]
- [Category B]
- [Category C]

Follow-up: Provide detailed info → Ask if they want to purchase → Guide to checkout

---

PATH 2: PRICING & PLANS
User: "What are your prices?"
Bot: "We have three flexible plans designed for different needs:

💡 Starter - $29/mo
✅ Feature 1
✅ Feature 2

🚀 Pro - $79/mo
✅ Everything in Starter
✅ Advanced features

💼 Enterprise - Custom
✅ Everything in Pro
✅ Dedicated support

Which plan sounds right for you?"

---

PATH 3: SUPPORT
User: "I need help"
Bot: "I'm here to help! What do you need assistance with?"
- Account issues
- Technical problems
- Billing questions
- General inquiry

---

ERROR HANDLING:
If unclear: "I'm not sure I understand. Could you rephrase that?"
If frustrated: "I sense you might be frustrated. Would you like me to connect you with our support team?"

---

ESCALATION:
If can't help: "Let me connect you with a team member who can better assist. One moment please..."

---

CLOSING:
"Is there anything else I can help you with today?"
→ Yes: Loop back to main menu
→ No: "Thank you for chatting! Have a wonderful day! 😊"`;
}

function generateLeadMagnetMockResponse(prompt: string): string {
  return `📚 EBOOK OUTLINE

TITLE: "[Topic]: The Complete Guide for [Target Audience]"

TABLE OF CONTENTS:

Introduction
- Why this matters now
- What you'll learn
- How to use this guide

Chapter 1: The Foundation
- Understanding the basics
- Common misconceptions
- Key principles

Chapter 2: Getting Started
- Step-by-step setup
- Essential tools & resources
- Quick wins

Chapter 3: Advanced Strategies
- Proven techniques
- Case studies
- Best practices

Chapter 4: Avoiding Common Mistakes
- Top 5 pitfalls
- How to course-correct
- Warning signs

Chapter 5: Scaling & Growth
- Next-level tactics
- Automation strategies
- Long-term planning

Chapter 6: Action Plan
- 30-day implementation roadmap
- Checklists
- Templates

Bonus Section: Resources
- Recommended tools
- Further reading
- Community links

Conclusion & Next Steps
- Key takeaways
- Your action items
- Special offer

CALL-TO-ACTION:
"Ready to implement what you've learned? Book a free consultation with our team to create your custom strategy."`;
}

function generateProposalMockResponse(prompt: string): string {
  return `📄 PROJECT PROPOSAL

EXECUTIVE SUMMARY
This proposal outlines a comprehensive solution for [Project Type] that will deliver measurable results and exceed expectations. Our approach combines industry best practices with innovative strategies tailored to your specific needs.

PROJECT OBJECTIVES
- Objective 1: [Specific, measurable goal]
- Objective 2: [Specific, measurable goal]
- Objective 3: [Specific, measurable goal]

SCOPE OF WORK

Phase 1: Discovery & Planning (Week 1-2)
- Initial consultation & requirements gathering
- Market research & competitor analysis
- Strategy development
- Project roadmap creation

Phase 2: Implementation (Week 3-6)
- Core deliverable development
- Quality assurance & testing
- Client feedback integration
- Optimization

Phase 3: Launch & Support (Week 7-8)
- Final delivery
- Team training
- Documentation
- 30-day post-launch support

DELIVERABLES
✅ [Deliverable 1] - [Description]
✅ [Deliverable 2] - [Description]
✅ [Deliverable 3] - [Description]
✅ Complete documentation
✅ Training materials

TIMELINE & MILESTONES
Week 2: Strategy approval
Week 4: Mid-project review
Week 6: Beta delivery
Week 8: Final launch

INVESTMENT
Total Investment: [Budget]
Payment Schedule:
- 50% upon contract signing
- 25% at mid-project milestone
- 25% upon completion

TERMS & CONDITIONS
- Project timeline: [X] weeks
- Revisions: Up to 3 rounds included
- Additional work billed at [rate]
- Payment terms: Net 30

WHY CHOOSE US
✨ [Years] of experience
✨ [Number] successful projects
✨ Proven track record
✨ Dedicated support

NEXT STEPS
1. Review this proposal
2. Schedule kickoff call
3. Sign agreement
4. Begin transformation

We're excited to partner with you on this journey!`;
}

function generateFollowUpMockResponse(prompt: string): string {
  return `Subject: Following up on our conversation

Hi [Client Name],

I hope this message finds you well! I wanted to reach out and see how things are progressing on your end.

It's been [time period] since we last connected, and I wanted to check in to see if you had any questions about [context/previous discussion].

I understand you're busy, so I wanted to make this easy for you:

Would any of these times work for a quick 15-minute call?
- [Option 1]
- [Option 2]
- [Option 3]

No pressure at all - I just want to make sure you have everything you need to move forward when you're ready.

Is there anything I can clarify or any additional information I can provide?

Looking forward to hearing from you!

Best regards,
[Your Name]

P.S. - I came across [relevant resource/article] that made me think of you. Thought you might find it helpful!`;
}
