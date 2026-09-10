from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import mm
from reportlab.platypus import (
    BaseDocTemplate, Frame, PageTemplate, Paragraph, Spacer, Table, TableStyle,
    PageBreak, KeepTogether
)

OUTPUT = r"C:\xampp\htdocs\cms\CMS_FEATURES.pdf"
PAGE_W, PAGE_H = A4

NAVY = colors.HexColor("#12345B")
BLUE = colors.HexColor("#1D65A6")
TEAL = colors.HexColor("#168C8C")
GOLD = colors.HexColor("#E4A11B")
INK = colors.HexColor("#1D2939")
MUTED = colors.HexColor("#667085")
PALE = colors.HexColor("#F3F7FB")
LINE = colors.HexColor("#D6E0EA")
RED = colors.HexColor("#B42318")

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(name="CoverTitle", parent=styles["Title"], fontName="Helvetica-Bold", fontSize=31, leading=36, textColor=colors.white, alignment=TA_LEFT, spaceAfter=14))
styles.add(ParagraphStyle(name="CoverSub", parent=styles["Normal"], fontName="Helvetica", fontSize=14, leading=21, textColor=colors.HexColor("#DCEAF7"), spaceAfter=10))
styles.add(ParagraphStyle(name="Section", parent=styles["Heading1"], fontName="Helvetica-Bold", fontSize=22, leading=27, textColor=NAVY, spaceBefore=4, spaceAfter=10))
styles.add(ParagraphStyle(name="SectionLead", parent=styles["Normal"], fontName="Helvetica", fontSize=10.5, leading=16, textColor=MUTED, spaceAfter=14))
styles.add(ParagraphStyle(name="FeatureTitle", parent=styles["Heading2"], fontName="Helvetica-Bold", fontSize=12.5, leading=16, textColor=NAVY, spaceAfter=4))
styles.add(ParagraphStyle(name="BodySmall", parent=styles["BodyText"], fontName="Helvetica", fontSize=9, leading=13, textColor=INK, spaceAfter=3))
styles.add(ParagraphStyle(name="Label", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=8.5, leading=12, textColor=BLUE))
styles.add(ParagraphStyle(name="TableHead", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=8, leading=10, textColor=colors.white, alignment=TA_CENTER))
styles.add(ParagraphStyle(name="TableCell", parent=styles["BodyText"], fontName="Helvetica", fontSize=7.4, leading=10, textColor=INK))
styles.add(ParagraphStyle(name="TableCellCenter", parent=styles["TableCell"], alignment=TA_CENTER))
styles.add(ParagraphStyle(name="Callout", parent=styles["BodyText"], fontName="Helvetica", fontSize=10, leading=15, textColor=INK))
styles.add(ParagraphStyle(name="Tiny", parent=styles["BodyText"], fontName="Helvetica", fontSize=7.5, leading=10, textColor=MUTED))


def header_footer(canvas, doc):
    canvas.saveState()
    if doc.page > 1:
        canvas.setStrokeColor(LINE)
        canvas.line(18 * mm, PAGE_H - 14 * mm, PAGE_W - 18 * mm, PAGE_H - 14 * mm)
        canvas.setFont("Helvetica-Bold", 8)
        canvas.setFillColor(NAVY)
        canvas.drawString(18 * mm, PAGE_H - 10.5 * mm, "HARIVANDANA COLLEGE")
        canvas.setFont("Helvetica", 8)
        canvas.setFillColor(MUTED)
        canvas.drawRightString(PAGE_W - 18 * mm, PAGE_H - 10.5 * mm, "CMS Feature Roadmap")
    canvas.setStrokeColor(LINE)
    canvas.line(18 * mm, 13 * mm, PAGE_W - 18 * mm, 13 * mm)
    canvas.setFont("Helvetica", 7.5)
    canvas.setFillColor(MUTED)
    canvas.drawString(18 * mm, 8.5 * mm, "Stakeholder planning document | 01 September 2026")
    canvas.drawRightString(PAGE_W - 18 * mm, 8.5 * mm, f"Page {doc.page}")
    canvas.restoreState()


doc = BaseDocTemplate(
    OUTPUT, pagesize=A4, leftMargin=18 * mm, rightMargin=18 * mm,
    topMargin=20 * mm, bottomMargin=18 * mm, title="Harivandana College CMS Feature Roadmap",
    author="Harivandana College"
)
frame = Frame(doc.leftMargin, doc.bottomMargin, doc.width, doc.height, id="normal")
doc.addPageTemplates([PageTemplate(id="main", frames=[frame], onPage=header_footer)])
story = []

# Cover
cover = Table([[Paragraph("HARIVANDANA<br/>COLLEGE", ParagraphStyle(name="Brand", fontName="Helvetica-Bold", fontSize=12, leading=15, textColor=GOLD)), ""]], colWidths=[70 * mm, 98 * mm], rowHeights=[20 * mm])
cover.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, -1), NAVY), ("VALIGN", (0, 0), (-1, -1), "MIDDLE"), ("LEFTPADDING", (0, 0), (-1, -1), 10 * mm)]))
story.append(cover)
hero = Table([[
    Paragraph("College Management System<br/>Feature Roadmap", styles["CoverTitle"]),
    Paragraph("PUBLIC EXPERIENCE<br/><font size='28'><b>10</b></font><br/>proposed features<br/><br/>INTERNAL PLATFORM<br/><font size='28'><b>16</b></font><br/>prioritized modules", ParagraphStyle(name="CoverStats", fontName="Helvetica", fontSize=9, leading=15, textColor=colors.white, alignment=TA_CENTER))
]], colWidths=[111 * mm, 57 * mm], rowHeights=[103 * mm])
hero.setStyle(TableStyle([("BACKGROUND", (0, 0), (0, 0), BLUE), ("BACKGROUND", (1, 0), (1, 0), TEAL), ("VALIGN", (0, 0), (-1, -1), "MIDDLE"), ("LEFTPADDING", (0, 0), (0, 0), 12 * mm), ("RIGHTPADDING", (0, 0), (0, 0), 9 * mm)]))
story.append(hero)
story.append(Spacer(1, 15 * mm))
story.append(Paragraph("A stakeholder-ready proposal for extending the current Laravel 12 campus platform into a stronger public website and a more complete digital campus.", ParagraphStyle(name="CoverIntro", parent=styles["BodyText"], fontSize=13, leading=20, textColor=INK)))
story.append(Spacer(1, 12 * mm))
story.append(Paragraph("Prepared from a repository audit and the implementation-ready backlog. Existing capabilities include admissions, academics, attendance, fees, examinations, timetables, materials, notices, hostel operations, certificates, WhatsApp outreach, APIs, and role-based dashboards.", styles["SectionLead"]))
story.append(PageBreak())

story.append(Paragraph("Executive overview", styles["Section"]))
story.append(Paragraph("The platform already handles the core student lifecycle after login, but its public presence is minimal: the root route redirects to login and the only substantive public workflow is the online admission form. This roadmap proposes a credible public-facing college website while retaining the detailed internal-module backlog already prepared for the campus application.", styles["SectionLead"]))

overview = [
    [Paragraph("RECOMMENDED SEQUENCE", styles["TableHead"]), Paragraph("OUTCOME", styles["TableHead"])],
    [Paragraph("1. Trust and discovery", styles["TableCell"]), Paragraph("Homepage, program catalog, notices, SEO, accessibility and multilingual foundations.", styles["TableCell"])],
    [Paragraph("2. Conversion and service", styles["TableCell"]), Paragraph("Apply/status journey, contact automation and document verification.", styles["TableCell"])],
    [Paragraph("3. Community growth", styles["TableCell"]), Paragraph("Alumni registration, stories, mentoring and opportunities.", styles["TableCell"])],
    [Paragraph("4. Internal modernization", styles["TableCell"]), Paragraph("Deliver the 16 prioritized campus modules in phased releases, beginning with quick wins.", styles["TableCell"])],
]
t = Table(overview, colWidths=[50 * mm, 118 * mm], repeatRows=1)
t.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, 0), NAVY), ("GRID", (0, 0), (-1, -1), .5, LINE), ("VALIGN", (0, 0), (-1, -1), "TOP"), ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, PALE]), ("LEFTPADDING", (0, 0), (-1, -1), 7), ("RIGHTPADDING", (0, 0), (-1, -1), 7), ("TOPPADDING", (0, 0), (-1, -1), 7), ("BOTTOMPADDING", (0, 0), (-1, -1), 7)]))
story.append(t)
story.append(Spacer(1, 7 * mm))
story.append(Paragraph("Current foundation", styles["FeatureTitle"]))
story.append(Paragraph("Laravel 12 and PHP 8.2+, server-rendered Blade and Bootstrap, MySQL, Sanctum API endpoints, dompdf document generation, and Meta WhatsApp Cloud API integration. Roles are management, staff and student.", styles["BodySmall"]))
story.append(Spacer(1, 4 * mm))
story.append(Paragraph("Decision lens", styles["FeatureTitle"]))
story.append(Paragraph("Prioritize features that improve prospective-student discovery, reduce admissions friction, establish public trust, or eliminate recurring administrative work. Security remediation should travel with Phase 1 rather than being deferred.", styles["BodySmall"]))
story.append(PageBreak())

public_features = [
    ("P1", "Public marketing homepage revamp", "As a prospective student or parent, I can understand the college, its programs and outcomes without signing in.", "A modern hero, about section, program highlights, faculty profiles, campus gallery, testimonials, achievements and recent news. Content should be manageable rather than hard-coded.", "Creates a credible first impression and turns the website into an admissions channel.", "M", "High"),
    ("P2", "Apply Now journey and application-status lookup", "As an applicant, I can apply without an account and later check progress using verified personal details.", "Improve the existing public /apply flow with clearer steps, confirmation and consent. Add a no-login status lookup using application reference plus email or phone, with rate limiting and neutral error messages.", "Reduces enquiry calls and makes the existing admissions pipeline transparent.", "M", "High"),
    ("P3", "SEO-friendly course and program catalog", "As a visitor, I can compare departments, programs, eligibility, duration, fees and career pathways.", "Create an index plus one stable public page per department/course, sourced from existing academic master data with optional public descriptions and media.", "Improves organic discovery and helps applicants choose the right program.", "M", "High"),
    ("P4", "Public notice board, news and events", "As a visitor, I can see admissions deadlines, achievements, events and public announcements.", "Surface only explicitly public notices and future calendar/event records. Provide categories, dates, detail pages, archive and featured items; never expose targeted staff/student notices.", "Keeps the site current while reusing the internal publishing workflow.", "S-M", "High"),
    ("P5", "Certificate and marksheet verification", "As an employer or institution, I can verify that a college document is genuine.", "Add a public verification page using a signed code or QR printed on PDFs. Return a minimal authenticity result and document metadata, with rate limits and privacy-safe output.", "Builds trust and reduces manual verification requests and document fraud.", "M", "High"),
    ("P6", "Alumni portal and directory", "As an alumnus, I can register, share outcomes and discover opportunities to support the college community.", "Provide moderated alumni registration, opt-in profiles, success stories, mentoring, reunions and job postings. Keep personal contact data private by default.", "Strengthens reputation, placements, mentoring and long-term engagement.", "L", "Medium"),
    ("P7", "Public contact and enquiry automation", "As a visitor, I can ask a question and receive immediate confirmation through my preferred channel.", "Add categorized enquiries, ownership and status tracking, spam protection, consent, email acknowledgements and optional WhatsApp templates through the existing service.", "Captures leads reliably and shortens response time without losing enquiries in personal inboxes.", "M", "High"),
    ("P8", "Multi-language public experience", "As a regional-language visitor, I can navigate and read essential admissions content comfortably.", "Introduce locale-aware routes and translations for navigation, homepage, program pages, admissions guidance, notices and forms. Define a content translation workflow and fallback language.", "Broadens access for students and parents and reduces misunderstanding.", "M-L", "Medium"),
    ("P9", "SEO and social-sharing foundation", "As the college, we want every public page to be discoverable, accurately previewed and measurable.", "Add unique titles/descriptions, canonical URLs, Open Graph metadata, structured data, sitemap.xml, meaningful robots.txt rules, redirects and privacy-respecting analytics.", "Makes public content discoverable and improves campaign sharing and attribution.", "S-M", "High"),
    ("P10", "Accessibility and mobile-responsiveness pass", "As a visitor on any device or with assistive technology, I can use the public site successfully.", "Target WCAG 2.2 AA: semantic headings, keyboard access, visible focus, form labels/errors, contrast, alt text, reduced motion, touch targets and testing across common mobile widths.", "Improves reach, usability and institutional quality while lowering legal and reputational risk.", "M", "High"),
]

story.append(Paragraph("Public Website Features", styles["Section"]))
story.append(Paragraph("These proposals complement - and do not duplicate - the internal campus backlog. They transform the current login-first entry point into a public information, trust and admissions experience.", styles["SectionLead"]))

def feature_card(item):
    code, title, feature_story, scope, value, effort, priority = item
    heading = Table([[Paragraph(code, ParagraphStyle(name=f"Code{code}", fontName="Helvetica-Bold", fontSize=10, textColor=colors.white, alignment=TA_CENTER)), Paragraph(title, styles["FeatureTitle"]), Paragraph(f"{priority}<br/>{effort}", ParagraphStyle(name=f"Meta{code}", fontName="Helvetica-Bold", fontSize=8, leading=11, textColor=TEAL, alignment=TA_CENTER))]], colWidths=[14 * mm, 128 * mm, 26 * mm])
    heading.setStyle(TableStyle([("BACKGROUND", (0, 0), (0, 0), BLUE), ("BOX", (0, 0), (-1, -1), .6, LINE), ("VALIGN", (0, 0), (-1, -1), "MIDDLE"), ("LEFTPADDING", (1, 0), (1, 0), 8), ("TOPPADDING", (0, 0), (-1, -1), 7), ("BOTTOMPADDING", (0, 0), (-1, -1), 5)]))
    details = Table([
        [Paragraph("STORY", styles["Label"]), Paragraph(feature_story, styles["BodySmall"])],
        [Paragraph("SCOPE", styles["Label"]), Paragraph(scope, styles["BodySmall"])],
        [Paragraph("VALUE", styles["Label"]), Paragraph(value, styles["BodySmall"])],
    ], colWidths=[20 * mm, 148 * mm])
    details.setStyle(TableStyle([("BOX", (0, 0), (-1, -1), .6, LINE), ("INNERGRID", (0, 0), (-1, -1), .35, LINE), ("BACKGROUND", (0, 0), (0, -1), PALE), ("VALIGN", (0, 0), (-1, -1), "TOP"), ("LEFTPADDING", (0, 0), (-1, -1), 7), ("RIGHTPADDING", (0, 0), (-1, -1), 7), ("TOPPADDING", (0, 0), (-1, -1), 5), ("BOTTOMPADDING", (0, 0), (-1, -1), 5)]))
    return [heading, details, Spacer(1, 5 * mm)]

for idx, item in enumerate(public_features):
    if idx and idx % 2 == 0:
        story.append(PageBreak())
        # ReportLab's first continuation frame can begin above the header after
        # the preceding compound table; reserve the header band explicitly.
        if idx == 2:
            story.append(Spacer(1, 24 * mm))
        story.append(Paragraph("Public Website Features - continued", styles["Section"]))
        story.append(Spacer(1, 2 * mm))
    story.extend(feature_card(item))

story.append(PageBreak())
story.append(Paragraph("Internal Platform Backlog", styles["Section"]))
story.append(Paragraph("The following is a condensed view of the 16-item implementation-ready backlog. Full touchpoints and acceptance criteria remain in FEATURES_TO_ADD.md.", styles["SectionLead"]))

internal = [
    ("F1", "Self-service password reset", "Neutral, expiring email/OTP recovery flow.", "S", "High"),
    ("F2", "Automated event notifications", "Queued attendance, fee, result and notice alerts.", "M", "High"),
    ("F3", "Online fee payment gateway", "Razorpay/UPI payment, webhook reconciliation and receipts.", "M", "High"),
    ("F4", "Parent / guardian portal", "Guardian view of attendance, fees, results and notices.", "M", "High"),
    ("F5", "Bulk student import", "CSV/Excel validation, dry run and error report.", "S", "High"),
    ("F6", "Management two-factor authentication", "TOTP protection with recovery codes.", "M", "Medium"),
    ("F7", "Audit log / activity trail", "Actor, action and before/after accountability.", "S", "Medium"),
    ("F8", "Library management", "Catalog, copies, issue/return, reservations and fines.", "M", "Medium"),
    ("F9", "Transcript and result publishing", "Controlled release and cross-semester PDF transcript.", "M", "Medium"),
    ("F10", "Timetable clash detection", "Prevent room/staff conflicts and add calendar view.", "M", "Medium"),
    ("F11", "Reports and export center", "Filtered attendance, fees, results and admissions exports.", "M", "Medium"),
    ("F12", "Analytics dashboard", "Trends and drill-down KPIs for management.", "M", "Medium"),
    ("F13", "Academic calendar and events", "Holidays, exams, events and dashboard visibility.", "S", "Medium"),
    ("F14", "QR self check-in attendance", "Time-bound class QR with staff confirmation.", "M", "Low"),
    ("F15", "Transport management", "Routes, stops, buses, assignments and fee tracking.", "M", "Low"),
    ("F16", "Hostel mess management", "Menus, meal attendance, billing and feedback.", "L", "Low"),
]
rows = [[Paragraph("ID", styles["TableHead"]), Paragraph("FEATURE", styles["TableHead"]), Paragraph("ONE-LINE SCOPE", styles["TableHead"]), Paragraph("EFFORT", styles["TableHead"]), Paragraph("PRIORITY", styles["TableHead"])]]
for fid, name, scope, effort, priority in internal:
    rows.append([Paragraph(fid, styles["TableCellCenter"]), Paragraph(f"<b>{name}</b>", styles["TableCell"]), Paragraph(scope, styles["TableCell"]), Paragraph(effort, styles["TableCellCenter"]), Paragraph(priority, styles["TableCellCenter"])])
t = Table(rows, colWidths=[12 * mm, 45 * mm, 78 * mm, 16 * mm, 17 * mm], repeatRows=1)
t.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, 0), NAVY), ("GRID", (0, 0), (-1, -1), .45, LINE), ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, PALE]), ("VALIGN", (0, 0), (-1, -1), "TOP"), ("LEFTPADDING", (0, 0), (-1, -1), 4), ("RIGHTPADDING", (0, 0), (-1, -1), 4), ("TOPPADDING", (0, 0), (-1, -1), 5), ("BOTTOMPADDING", (0, 0), (-1, -1), 5)]))
story.append(t)
story.append(Spacer(1, 6 * mm))
quick = Table([[Paragraph("PHASE 1 QUICK WINS", styles["TableHead"]), Paragraph("F1 Password reset  |  F5 Bulk import  |  F7 Audit log  |  F13 Academic calendar", styles["Callout"])]], colWidths=[45 * mm, 123 * mm])
quick.setStyle(TableStyle([("BACKGROUND", (0, 0), (0, 0), TEAL), ("BACKGROUND", (1, 0), (1, 0), colors.HexColor("#E9F7F6")), ("BOX", (0, 0), (-1, -1), .7, TEAL), ("VALIGN", (0, 0), (-1, -1), "MIDDLE"), ("LEFTPADDING", (0, 0), (-1, -1), 8), ("TOPPADDING", (0, 0), (-1, -1), 8), ("BOTTOMPADDING", (0, 0), (-1, -1), 8)]))
story.append(quick)
story.append(PageBreak())

story.append(Paragraph("Known Issues to Fix Alongside", styles["Section"]))
story.append(Paragraph("These security issues are already present in the current codebase. Treat them as release criteria for the first delivery phase, not optional future enhancements.", styles["SectionLead"]))
issues = [
    ("01", "Add login rate limiting", "POST /login is currently unthrottled. Apply a named rate limiter or throttle middleware and log repeated failures."),
    ("02", "Remove unsafe login paths", "Stop login by full name, prefer unique identifiers, and remove the dead /quick-login/{role} route that references a missing method."),
    ("03", "Strengthen passwords", "Replace seeded/default 'password' credentials, require at least eight characters with stronger rules, and force rotation."),
    ("04", "Encrypt integration secrets", "The WhatsApp access token is stored as a plain fillable setting. Add encrypted storage/casting and rotate any exposed token."),
    ("05", "Remove risky or stale code", "Remove the loose export_mysql.php script, fix the stale Enquiry seeder, and confirm database exports are never web-accessible."),
]
for num, title, body in issues:
    box = Table([[Paragraph(num, ParagraphStyle(name=f"Issue{num}", fontName="Helvetica-Bold", fontSize=14, textColor=colors.white, alignment=TA_CENTER)), Paragraph(f"<b>{title}</b><br/><font size='9'>{body}</font>", styles["Callout"])]], colWidths=[18 * mm, 150 * mm])
    box.setStyle(TableStyle([("BACKGROUND", (0, 0), (0, 0), RED), ("BACKGROUND", (1, 0), (1, 0), colors.HexColor("#FFF4F2")), ("BOX", (0, 0), (-1, -1), .7, colors.HexColor("#F4B8B3")), ("VALIGN", (0, 0), (-1, -1), "MIDDLE"), ("LEFTPADDING", (0, 0), (-1, -1), 8), ("RIGHTPADDING", (0, 0), (-1, -1), 8), ("TOPPADDING", (0, 0), (-1, -1), 9), ("BOTTOMPADDING", (0, 0), (-1, -1), 9)]))
    story.append(box)
    story.append(Spacer(1, 4 * mm))

story.append(Spacer(1, 4 * mm))
story.append(Paragraph("Implementation notes", styles["FeatureTitle"]))
story.append(Paragraph("External decisions are required before online payments (vendor and credentials), WhatsApp notification templates/costs, and the management 2FA package. Public status lookup and verification endpoints must be rate-limited and disclose only the minimum information. Public notices require an explicit visibility flag so internal audiences remain private.", styles["BodySmall"]))
story.append(Spacer(1, 6 * mm))
source = Table([[Paragraph("SOURCE DOCUMENTS", styles["TableHead"]), Paragraph("FEATURES_AND_IMPROVEMENTS.md - repository audit and known issues<br/>FEATURES_TO_ADD.md - full 16-feature implementation specifications", styles["Callout"])]], colWidths=[45 * mm, 123 * mm])
source.setStyle(TableStyle([("BACKGROUND", (0, 0), (0, 0), NAVY), ("BACKGROUND", (1, 0), (1, 0), PALE), ("BOX", (0, 0), (-1, -1), .7, LINE), ("VALIGN", (0, 0), (-1, -1), "MIDDLE"), ("LEFTPADDING", (0, 0), (-1, -1), 8), ("TOPPADDING", (0, 0), (-1, -1), 8), ("BOTTOMPADDING", (0, 0), (-1, -1), 8)]))
story.append(source)

doc.build(story)
print(OUTPUT)
