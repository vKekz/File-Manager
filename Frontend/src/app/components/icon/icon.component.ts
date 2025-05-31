import { Component, Input } from "@angular/core";
import { Router } from "@angular/router";

@Component({
  selector: "app-icon",
  imports: [],
  templateUrl: "./icon.component.html",
  styleUrl: "./icon.component.css",
})
export class IconComponent {
  @Input()
  public width: number = 256;

  @Input()
  public height: number = 256;

  constructor(private readonly router: Router) {}

  protected async goToHome() {
    await this.router.navigateByUrl("/");
  }
}
