import { Component } from "@angular/core";
import { RouterOutlet } from "@angular/router";
import { UserService } from "../services/user.service";

@Component({
  selector: "app-root",
  imports: [RouterOutlet],
  templateUrl: "./app.component.html",
  styleUrl: "./app.component.css",
})
export class AppComponent {
  constructor(private readonly userService: UserService) {}

  test() {
    this.userService.getUsers().subscribe((data) => {
      console.log(data);
    });
  }
}
