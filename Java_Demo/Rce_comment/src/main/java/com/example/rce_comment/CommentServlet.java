package com.example.rce_comment;

import org.springframework.expression.Expression;
import org.springframework.expression.ExpressionParser;
import org.springframework.expression.spel.standard.SpelExpressionParser;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.List;

@WebServlet("/comment")
public class CommentServlet extends HttpServlet {

    private List<String> comments = new ArrayList<>();

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        response.setContentType("text/html;charset=UTF-8");
        PrintWriter out = response.getWriter();

        // 获取用户评论
        String comment = request.getParameter("comment");

        if (comment != null) {
            try {
                // 模拟对评论进行一些动态处理，这里使用 SpEL 解析评论内容
                ExpressionParser parser = new SpelExpressionParser();
                Expression exp = parser.parseExpression(comment);
                Object result = exp.getValue();

                // 存储评论
                comments.add(comment);

                out.println("评论提交成功！");
                out.println("解析结果: " + result);
            } catch (Exception e) {
                out.println("评论解析出错: " + e.getMessage());
            }
        } else {
            out.println("请输入评论内容");
        }
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        response.setContentType("text/html;charset=UTF-8");
        PrintWriter out = response.getWriter();

        // 显示所有评论
        out.println("<h1>所有评论</h1>");
        for (String comment : comments) {
            out.println("<p>" + comment + "</p>");
        }
    }
}