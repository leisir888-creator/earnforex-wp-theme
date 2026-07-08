# GitHub Actions 自动部署配置指南

## 必须在 GitHub 仓库 Settings → Secrets 添加：

| Secret | 说明 | 示例 |
|--------|------|------|
| FTP_HOST | FTP 服务器 | ftp.fxtraderskit.com |
| FTP_USERNAME | FTP 用户名 | your_ftp_user |
| FTP_PASSWORD | FTP 密码 | your_ftp_pass |
| FTP_REMOTE_DIR | 远程目录 | /wp-content/themes/earnforex-wp-theme/ |

## 触发部署
- push 到 main 分支
- 或 Actions 页面手动 Run workflow
