print("Enter your input (press Enter twice to finish):")
lines = []
while True:
    line = input()
    if line == "" and lines:  # Empty line and we have some content
        break
    lines.append(line)

user_input = "\n".join(lines)